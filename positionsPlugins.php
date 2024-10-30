<?php
	/**
	 * Plugin Name: ClayHR JobBoard
	 * Description: This plugin is used to display the open job positions at BizMerlinHR JobBoard
	 * Version: 2.0
	 * Requires at least: 5.2
	 * Requires PHP: 7.2
	 * Author: BizMerlin
	 * Author URI: https://www.clayhr.com/
	 * License: GPL v2 or later
	 * License URI: https://www.gnu.org/licenses/gpl-2.0.html.
	 *
	 * @package    BizMerlinHR_JobBoard
	 */



if ( ! class_exists( 'BizMerlinHR_Job_Positions_Plugin' ) ) {
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * @package    BizMerlinHR_JobBoard
	 */
	class BizMerlinHR_Job_Positions_Plugin {
		/**
		 * The API Base URL for BizMerlinHR API.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $api_base_url    The API Base URL for BizMerlinHR API.
		 */
		private $api_base_url;
		/**
		 * The API Base URL for BizMerlinHR API.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $api_base_url    The API Base URL for BizMerlinHR API.
		 */
		private $jobboard_base_url;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->options = get_option( 'BizMerlinHR_jobboards_settings' );
			$this->subdomain = $this->options['subdomain'];
			$this->api_base_url = 'https://' . $this->subdomain . '.bizmerlin.net/job-board/';
			$this->jobboard_base_url = 'https://' . $this->subdomain . '.bizmerlin.net/jobboard/#/';
			add_action( 'wp_enqueue_scripts', array( $this, 'callback_for_setting_up_scripts' ) );
			add_action( 'admin_menu', array( $this, 'add_plugin_option' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
			add_shortcode( 'BizMerlin_job_listings', array( $this, 'job_positions_code' ) );
		}

		/**
		 * Add Options to Plugin Settings Page.
		 *
		 * @since    1.0.0
		 */
		public function add_plugin_option() {
			// This page will be under 'Settings'.
			add_options_page( 'Settings Admin', 'ClayHR Settings', 'manage_options', 'BizMerlinHR_jobboards-settings-admin', array( $this, 'create_admin_page' ) );
		}

		public function callback_for_setting_up_scripts() {
			wp_register_style( 'styling', plugin_basename( __FILE__ ) . '/positionsPlugins.css');
			wp_enqueue_style( 'styling' );
		}

		/**
		 * Create Plugin Settings Page.
		 *
		 * @since    1.0.0
		 */
		public function create_admin_page() {
			// Set class property.
			$this->options = get_option( 'BizMerlinHR_jobboards_settings' );
?>
		<div class='wrap'>
		<h2>ClayHR JobBoard Settings</h2>
		<form method='post' action='options.php'>
			<?php
				// This prints out all hidden setting fields.
				settings_fields( 'BizMerlinHR_jobboards_settings_group' );
				do_settings_sections( 'BizMerlinHR_jobboards-settings-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

		/**
		 * Create Plugin Settings Page.
		 *
		 * @since    1.0.0
		 */
		public function page_init() {
			register_setting(
				'BizMerlinHR_jobboards_settings_group', // Option group.
				'BizMerlinHR_jobboards_settings', // Option name.
				array( $this, 'sanitize' ) // Sanitize.
			);

			add_settings_section(
				'BizMerlinHR_jobboards_section', // ID.
				'Subdomain settings', // Title.
				array( $this, 'print_section_info' ), // Callback.
				'BizMerlinHR_jobboards-settings-admin' // Page.
			);

			add_settings_field(
				'subdomain',  // ID.
				'Subdomain', // Title.
				array( $this, 'subdomain_callback' ), // Callback.
				'BizMerlinHR_jobboards-settings-admin', // Page.
				'BizMerlinHR_jobboards_section' // Section.
			);
		}


		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys.
		 */
		public function sanitize( $input ) {
			$new_input = array();
			if ( isset( $input['subdomain'] ) ) {
				$new_input['subdomain'] = sanitize_text_field( $input['subdomain'] );
			}
			return $new_input;
		}

		/**
		 * Print the Section text
		 */
		public function print_section_info() {
			echo '<p>Enter your subdomain below:<br />and then use the <strong>[BizMerlin_job_listings]</strong> shortcode to display the content.</p>';
			echo '<strong>To know your subdomain, refer to your JobBoard URL which looks something like this: SUBDOMAIN.bizmerlin.net/jobboard</strong>';
		}

		/**
		 * Get the settings option array and print one of its values
		 */
		public function subdomain_callback() {
			printf( "<input type='text' id='subdomain' name='BizMerlinHR_jobboards_settings[subdomain]' value='%s' />", isset( $this->options['subdomain'] ) ? esc_attr( $this->options['subdomain'] ) : '' );
		}
		
		/**
		 * Get the settings option array and print one of its values
		 */
		public function search_callback() {
			printf( "<input type='text' id='allowsearch' name='BizMerlinHR_jobboards_search_settings[allowsearch]' value='%s' />", isset( $this->options['allowsearch'] ) ? esc_attr( $this->options['allowsearch'] ) : '' );
		}

		/**
		 * Create html list of positions for mentioned subdomain.
		 */
		public function job_positions_code() {
			if ( isset( $this->subdomain ) && '' !== $this->subdomain ) {
				$positions = $this->get_bizmerlinhr_positions();
				$locations = $this->get_bizmerlinhr_locations();
				$search = $this->search_positions();
				$output .= "<div class='search_dialog'>";
				$output .= "<form action='" . esc_url( $_SERVER['REQUEST_URI'] ) . "' method='POST'>";
				$output .= "<input type='text' id='search' name='search' placeholder='search' value='" . $search['text'] . "'>";
				$output .= "<select id='location' name='location'>";
				if($search['location'] !== "") {
					$output .= "<option value=''>Select Location</option>";
				} else {
					$output .= "<option value='' selected>Select Location</option>";
				}
				foreach ( $locations as $location ) {
					$selected = "";
					if($search['location'] !== "" && strval($location['id']) === strval($search['location'])){
						$selected = "selected";
					}
					$output .= "<option value='" . $location['id'] . "' " . $selected . ">" . $location['name'] . "</option>";
				}
				$output .= "</select>";
				$output .= "<button type='submit' id='bizmerlin_search' name='bizmerlin_search'>Search</button>";
				$output .= "</form>";
				$output .= "</div>";
				$output .= "<div class='job-section'>";
				$output .= "<ul class='job-listings'>";
				foreach ( $positions as $position ) {
					$position_name = urlencode($position['seoname']);
					$position_url = $this->jobboard_base_url . 'position/view/' . $position['positionid'] . '/' . $position_name;
					$output .= '<li class="job-listing">';
					$output .= '<a class="posting-title" href="' . $position_url . '" target="blank">';
					$output .= '<h2>' . $position['name'] . '</h2>';
					$output .= '</a>';
					if($position['description']) {
						$output .= '<h4> Description </h4>';
						$output .= $position['description'];
					}
					if ($position['locationModel'] != null) {
						$output .= '<h4> Location </h4>';
						$output .= '<p>' . $position['locationModel']['locationName'] . '</p>';
					}
					if ($position['duedate']) {
						$output .= '<h4>Last Date to Apply</h4>';
						$output .= '<p>' . $position['duedate'] . '</p>';
					}
					$output .= '<br />';
					$output .= '<a href="' . $position_url . '" class="button" target="blank">Apply Now</a>';
					$output .= '</li>';
				}
			}
			$output .= '</ul>';
			$output .= '</div>';
			return $output;
		}
		
		/**
		 * Search btn Listener		 *
		 */
		public function search_positions() {
			// if the submit button is clicked, send the email
			$bizmerlinhr_search = array(
				'text' => '',
				'location' => ''
			);
				
			if ( isset( $_POST['bizmerlin_search'] ) ) {
				$search = $_POST["search"];
				$location = $_POST["location"];
				$bizmerlinhr_search = array(
					'text' => $search,
					'location' => $location
				);
			}
			return $bizmerlinhr_search;
		}

		/**
		 * Send HTTP Request and return the response.
		 *
		 * @param string $endpoint  The endpoint for api call.
		 */
		public function send_request( $endpoint ) {
			$api_url = $this->api_base_url . '/' . $endpoint;
			$json_data = wp_remote_retrieve_body( wp_remote_get( $api_url ) );
			$response = json_decode( $json_data, true );
			return $response;
		}

		/**
		 * Fetch list of positions for mentioned subdomain.
		 */
		public function get_bizmerlinhr_positions() {
			// Get any existing copy of our transient data.
			$bizmerlinhr_data = get_transient( 'BizMerlinHR_positions' );
			$search = $this->search_positions();
			if ( false === $bizmerlinhr_data ) {
				// It wasn't there, so make a new API Request and regenerate the data.
				$positions = $this->send_request( 'position/api/getpositions/' );
				if ( '' !== $positions ) {
					$bizmerlinhr_data = array();
					
					foreach ( $positions['positionModelList'] as $item ) {
						if($search['text'] !== '' && str_contains($item['name'], $search['text']) === false) {
							continue;
						}
						if($search['location'] !== '' && $item['locationModel'] !== null && strval($item['locationModel']['locationId']) !== strval($search['location'])) {
							continue;
						}
						$bizmerlinhr_position = array(
							'positionid' => $item['positionid'],
							'name' => $item['name'],
							'seoname' => $item['seoUrl'],
							'description' => $item['description'],
							'locationModel' => $item['locationModel'],
							'duedate' => $item['applicationDueDate']
						);
						array_push( $bizmerlinhr_data, $bizmerlinhr_position );
					}
				}
			} else {
				// Get any existing copy of our transient data.
				$bizmerlinhr_data = unserialize( get_transient( 'BizMerlinHR_positions' ) );
			}
			// Finally return the data.
			return $bizmerlinhr_data;
		}
		
		public function get_bizmerlinhr_locations() {
			// Get any existing copy of our transient data.
			$bizmerlinhr_data = get_transient( 'BizMerlinHR_locations' );
			if ( false === $bizmerlinhr_data ) {
				// It wasn't there, so make a new API Request and regenerate the data.
				$locations = $this->send_request( 'lookup/api/getLookupData?type=location' );
				if ( '' !== $locations ) {
					$bizmerlinhr_data = array();
					foreach ( $locations['lookupData'] as $item ) {
						$bizmerlinhr_location = array(
							'id' => $item['id'],
							'name' => $item['name'],
						);
						array_push( $bizmerlinhr_data, $bizmerlinhr_location );
					}
				}
			} else {
				// Get any existing copy of our transient data.
				$bizmerlinhr_data = unserialize( get_transient( 'BizMerlinHR_locations' ) );
			}
			// Finally return the data.
			return $bizmerlinhr_data;
		}

		/**
		 * Flush Old Positions Data.
		 */
		public function flush_stored_information() {
			// Delete transient to force a new pull from the API.
			delete_transient( 'BizMerlinHR_positions' );
		}
	}

		new BizMerlinHR_Job_Positions_Plugin();

}

/**
 * Add settings link on plugin page.
 *
 * @param array $links Contains all settings links.
 */
function bizmerlinhrsettingslink( $links ) {
	$settings_link = "<a href='options-general.php?page=BizMerlinHR_jobboards-settings-admin'>Settings</a>";
	array_unshift( $links, $settings_link );
	return $links;
}

$plugin_path = plugin_basename( __FILE__ );
add_filter( 'plugin_action_links_' . $plugin_path, 'bizmerlinhrsettingslink' );
