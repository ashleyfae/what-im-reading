<?php
/**
 * Goodreads Shelf Widget
 *
 * @package   what-im-reading
 * @copyright Copyright (c) 2020, Ashley Evans
 * @license   GPL2+
 */
class What_Im_Reading_Widget extends WP_Widget {

	/**
	 * The Goodreads API key
	 *
	 * @var string
	 * @access private
	 */
	private $api_key;

	/**
	 * The Goodreads user ID
	 *
	 * @var int
	 * @access private
	 */
	private $goodreads_id;

	/**
	 * The name of the shelf to get books from.
	 *
	 * @var string
	 * @access private
	 */
	private $shelf;

	/**
	 * The maximum number of results to show.
	 *
	 * @var int
	 * @access private
	 */
	private $limit;

	/**
	 * Sets up the widget's name, etc.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct(
			'what_im_reading', // Base ID
			__( 'Goodreads Shelf', 'what-im-reading' ), // Name
			array( 'description' => __( 'Displays books from one of your Goodreads shelves', 'what-im-reading' ), ) // Args
		);

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see    WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @access public
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title         = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$this->api_key = $instance['api_key'];
		$goodreads_id  = $instance['goodreads_id'];
		$shelf         = $instance['shelf'] ? $instance['shelf'] : 'currently-reading';
		$limit         = $instance['limit'] ? $instance['limit'] : 5;
		$format        = $instance['format'];
		$link_text     = $instance['link_text'];

		echo $args['before_widget'];

		// Display the widget title.
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// If there's no API key filled out - bail.
		if ( empty( $this->api_key ) ) {
			echo '<p>' . __( 'Error: You need to enter your Goodreads API key in the widget settings.', 'what-im-reading' ) . '</p>';
			echo $args['after_widget'];

			return;
		}

		// If there's no user ID filled out - bail.
		if ( empty( $goodreads_id ) ) {
			echo '<p>' . __( 'Error: You need to enter your Goodreads user ID number in the widget.', 'what-im-reading' ) . '</p>';
			echo $args['after_widget'];

			return;
		}

		$this->goodreads_id = $goodreads_id;
		$this->shelf        = $shelf;
		$this->limit        = $limit;

		$books = $this->query_goodreads( $format );

		do_action( 'what-im-reading/widget/before-shelf', $books, $format, $this );

		echo '<div class="grshelf-' . esc_attr( sanitize_html_class( $format ) ) . '">' . $books . '</div>';

		echo '<div class="gr-shelf-link"><a href="https://www.goodreads.com/review/list/' . urlencode( $goodreads_id ) . '?shelf=' . urlencode( $shelf ) . '" target="_blank">' . $link_text . '</a></div>';

		do_action( 'what-im-reading/widget/after-shelf', $books, $format, $this );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see    WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @access public
	 * @return void
	 */
	public function form( $instance ) {

		$instance     = wp_parse_args( (array) $instance, array(
			'title'        => __( 'Currently Reading', 'what-im-reading' ),
			'api_key'      => '',
			'goodreads_id' => '',
			'shelf'        => 'currently-reading',
			'limit'        => 5,
			'format'       => 'covers',
			'link_text'    => __( 'Visit my shelf on Goodreads', 'what-im-reading' )
		) );
		$title        = $instance['title'];
		$api_key      = $instance['api_key'];
		$goodreads_id = $instance['goodreads_id'];
		$shelf        = $instance['shelf'];
		$limit        = $instance['limit'];
		$format       = $instance['format'];
		$link_text    = $instance['link_text'];

		do_action( 'what-im-reading/widget/form/before', $instance, $this );
		?>

		<p>
			<strong><?php _e( 'Instructions', 'what-im-reading' ); ?></strong> <br>
			<?php printf( __( 'In order to use this widget you must first retrieve and enter your Goodreads API key. Read the <a href="%s" target="_blank">plugin instructions</a> for help on how to do this. In order for the plugin to retrieve the books from your shelf, you must also have your Goodreads profile set to public.', 'what-im-reading' ), 'https://www.nosegraze.com/what-im-reading/' ); ?>
		</p>

		<p>
			<?php _e( 'When prompted for your Goodreads user ID number, that is the set of digits in your Goodreads profile URL. Example:', 'what-im-reading' ); ?>
		</p>

		<p>
			https://www.goodreads.com/user/show/
			<mark>8769426</mark>
			-ashley
		</p>

		<p>
			<?php _e( 'The highlighted digits are the Goodreads ID number.', 'what-im-reading' ); ?>
		</p>

		<p>
			<strong><?php _e( 'Your shelf information will update once every six hours.', 'what-im-reading' ); ?></strong>
		</p>

		<hr>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'what-im-reading' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API Key:', 'what-im-reading' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" type="text" value="<?php echo esc_attr( $api_key ); ?>">
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'goodreads_id' ); ?>"><?php _e( 'Goodreads ID Number:', 'what-im-reading' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'goodreads_id' ); ?>" name="<?php echo $this->get_field_name( 'goodreads_id' ); ?>" type="number" value="<?php echo esc_attr( $goodreads_id ); ?>">
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'shelf' ); ?>"><?php _e( 'Shelf Name:', 'what-im-reading' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'shelf' ); ?>" name="<?php echo $this->get_field_name( 'shelf' ); ?>" type="text" value="<?php echo esc_attr( $shelf ); ?>">
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Maximum Number of Results (200 max):', 'what-im-reading' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>">
			</label>
		</p>

		<p>
			<?php _e( 'Format:', 'what-im-reading' ); ?> <br>
			<input type="radio" <?php checked( $format, 'covers' ); ?> name="<?php echo $this->get_field_name( 'format' ); ?>" id="<?php echo $this->get_field_id( 'format' ); ?>_covers" value="covers">
			<label for="<?php echo $this->get_field_id( 'format' ); ?>_covers"><?php _e( 'Book covers only', 'what-im-reading' ); ?></label>
			<br>
			<input type="radio" <?php checked( $format, 'details' ); ?> name="<?php echo $this->get_field_name( 'format' ); ?>" id="<?php echo $this->get_field_id( 'format' ); ?>_details" value="details">
			<label for="<?php echo $this->get_field_id( 'format' ); ?>_details"><?php _e( 'Cover, title, author', 'what-im-reading' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link Text (use "Goodreads" in the text to comply with their Terms of Service):', 'what-im-reading' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>">
			</label>
		</p>
		<?php
		do_action( 'what-im-reading/widget/form/after', $instance, $this );

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see    WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @access public
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['api_key']      = ( ! empty( $new_instance['api_key'] ) ) ? sanitize_text_field( $new_instance['api_key'] ) : '';
		$instance['goodreads_id'] = is_numeric( $new_instance['goodreads_id'] ) ? intval( $new_instance['goodreads_id'] ) : null;
		$instance['shelf']        = ( ! empty( $new_instance['shelf'] ) ) ? sanitize_text_field( $new_instance['shelf'] ) : 'currently-reading';
		$instance['limit']        = ( ! empty( $new_instance['limit'] ) && is_numeric( $new_instance['limit'] ) && $new_instance['limit'] >= 1 && $new_instance['limit'] <= 200 ) ? intval( $new_instance['limit'] ) : 5;
		$instance['format']       = sanitize_text_field( $new_instance['format'] );
		$instance['link_text']    = sanitize_text_field( $new_instance['link_text'] );

		// Delete the shelf cache when the widget is updated.
		$this->goodreads_id = $instance['goodreads_id'];
		if ( ! empty( $this->id ) ) {
			delete_option( 'ubb_What_Im_Reading_widget_' . sanitize_title( $this->id ) );
		}

		return apply_filters( 'what-im-reading/widget/update', $instance, $new_instance, $old_instance, $this );
	}

	/**
	 * Fetches the XML shelf data from Goodreads and parses it.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function query_goodreads( $format ) {
		// Get the cache
		$option       = get_option( 'ubb_What_Im_Reading_widget_' . sanitize_title( $this->id ) );
		$cached_value = $option ? $option : array( 'shelf' => '', 'expires' => 0 );

		// If the cached value exists and hasn't expired yet, return that.
		if ( ! empty( $cached_value ) && is_array( $cached_value ) && isset( $cached_value['shelf'] ) && ! empty( $cached_value['shelf'] ) && isset( $cached_value['expires'] ) && $cached_value['expires'] > time() ) {
			return $cached_value['shelf'];
		}

		$shelf_html = '';

		// Get the amount of time we want to cache for.
		$expiry_time_error   = HOUR_IN_SECONDS / 2; // 30 minutes
		$expiry_time_success = HOUR_IN_SECONDS * 6; // 6 hours

		$url = 'https://www.goodreads.com/review/list/' . urlencode( $this->goodreads_id ) . '?format=xml&key=' . urlencode( $this->api_key ) . '&v=2&shelf=' . urlencode( $this->shelf ) . '&per_page=' . urlencode( $this->limit );

		$response = wp_remote_get( $url );

		// There was an error - bail.
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
			// Update the expiry time to 30 minutes from now.
			$cached_value['expires'] = time() + $expiry_time_error;

			// Return the cached value since we don't have one.
			return $cached_value['shelf'];
		}

		// Get the raw body data.
		$data_raw = wp_remote_retrieve_body( $response );

		$shelf = new SimpleXMLElement( $data_raw );

		// If there are no books - bail.
		if ( ! $shelf || ! $shelf->reviews ) {
			// Update the expiry time to 30 minutes from now.
			$cached_value['expires'] = time() + $expiry_time_error;

			// Return the cached value since we don't have one.
			return $cached_value['shelf'];
		}

		$books = $shelf->reviews->review;

		// Loop through each book on their shelf.
		foreach ( $books as $review ) {
			$book = $review->book;

			$shelf_html .= $this->format_book( $book, $format );
		}

		// Update the cache
		$new_value = array(
			'shelf'   => $shelf_html,
			'expires' => time() + $expiry_time_success
		);
		update_option( 'ubb_What_Im_Reading_widget_' . sanitize_title( $this->id ), $new_value );

		return apply_filters( 'what-im-reading/widget/query-goodreads/html', $shelf_html, $books, $format, $this );
	}

	/**
	 * Formats the XML data according to the option selected.
	 *
	 * @param $book
	 * @param $format
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function format_book( $book, $format ) {
		$title     = (string) $book->title;
		$image_url = (string) $book->image_url;
		$link      = (string) $book->link;
		$synopsis  = (string) $book->description;

		// Get the array of author names.
		$author_names = array();
		$authors      = $book->authors;

		switch ( $format ) {
			case 'details' :
				// Shows the cover floated to the left with book details to the right.
				foreach ( $authors as $author ) {
					$author_names[] = '<a href="' . esc_url( $author->author->link ) . '" target="_blank">' . esc_html( $author->author->name ) . '</a>';
				}

				$authors = implode( ', ', $author_names );

				ob_start();
				?>
				<div class="grshelf-shelf-book">
					<a href="<?php echo esc_url( $link ); ?>" target="_blank">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( strip_tags( $title ) ); ?>" class="alignleft">
					</a>

					<span class="grshelf-book-title"><a href="<?php echo esc_url( $link ); ?>" target="_blank"><?php echo esc_html( $title ); ?></a></span>
					<span class="grshelf-by"><?php _e( 'by', 'what-im-reading' ); ?></span>
					<span class="grshelf-authors"><?php echo $authors; ?></span>
				</div>
				<?php
				$shelf = ob_get_clean();
				break;

			default:
				foreach ( $authors as $author ) {
					$author_names[] = $author->author->name;
				}

				$authors = implode( ', ', $author_names );
				// The default format just shows one book after the other.
				$shelf = '<a href="' . esc_url( $link ) . '" target="_blank"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( strip_tags( sprintf( __( '%1$s by %2$s', 'what-im-reading' ), $title, $authors ) ) ) . '"></a>';
		}

		return apply_filters( 'what-im-reading/widget/format-book', $shelf, $book, $format, $this );
	}

}