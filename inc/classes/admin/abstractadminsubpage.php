<?php
/**
 * Class to create the admin subpages for the Twitter Scheduler plugin.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Admin;

/**
 * An abstract class that should be used to create admin subpages in a plugin.
 *
 * @since  0.1.0
 */
abstract class AbstractAdminSubpage {

	/**
	 * The ID used to identify this page and it's assosiated settings.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $id = null;

	/**
	 * Returns the id that this class holds.
	 *
	 * This is used as this ID of this page when registering.
	 *
	 * @since  0.1.0
	 * @method get_id
	 * @return string|null
	 */
	public function get_id() {
		return $this->$id;
	}

	/**
	 * The parent pages id (since this is to create subpages).
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $parent_id = TWSC_ADMIN_PAGE_ID;

	/**
	 * Returns the parent_id that the class holds.
	 *
	 * This is used as a master page ID to register other subpages to. The page
	 * that is being used as parent needs registered before any child pages.
	 *
	 * @since  0.1.0
	 * @method get_parent_id
	 *
	 * @return string|null
	 */
	public function get_parent_id() {
		return $this->parent_id;
	}

	/**
	 * The title to use for the registered page.
	 *
	 * A string title to use for the page, this should be passed in via a i18n
	 * compatible function when being set.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $title = null;

	/**
	 * Returns the title the class holds.
	 *
	 * @since  0.1.0
	 * @method get_title
	 *
	 * @return string|null
	 */
	public function get_title() {
		return $this->title;
	}
	/**
	 * The is the public callable method that hooks in all our actions to make
	 * this page registered in the admin area.
	 *
	 * @since  0.1.0
	 * @method register
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );
		add_filter( 'twsc_options_tabs', [ $this, 'add_tab' ] );
	}

	/**
	 * Adds this settings page to the admin as a subpage of the paent_id.
	 *
	 * @since  0.1.0
	 * @method add_admin_menu
	 */
	public function add_admin_menu() {
		$result = add_submenu_page( $this->parent_id, $this->title, $this->title, 'manage_options', $this->id, [ $this, 'render' ] );
	}

	/**
	 * Setup the settings and register them to a section matching this page
	 * id in this method when extending.
	 *
	 * @since  0.1.0
	 * @method settings_init
	 */
	abstract public function settings_init();

	/**
	 * Renders the page option, includes a title, tab navigation and the group
	 * of settings for this page id.
	 *
	 * @since  0.1.0
	 * @method render
	 */
	public function render() {
		?>
		<h1><?php echo esc_html( $this->title ); ?></h1>
		<?php
		// output the tabs.
		$this->options_tabs( $this->id );
		?>
		<div class="sosc-admin-col sosc-admin-left">
			<form action='options.php' method='post'>

				<?php
				settings_fields( $this->id );
				do_settings_sections( $this->id );
				submit_button();
				?>

			</form>
		</div>
		<?php
	}

	/**
	 * This is a filter function to add this page id to the array of all pages
	 * used when building out the navigation.
	 *
	 * @since  0.1.0
	 * @method add_tab
	 *
	 * @param  array $tabs a maybe updated array of settings page ids.
	 */
	public function add_tab( $tabs ) {
		$tabs[] = [ $this->id, $this->title ];
		return $tabs;
	}

	/**
	 * Function capable of generating a tab based navigation for use on the
	 * plugins settings pages.
	 *
	 * @since  0.1.0
	 * @method options_tabs
	 *
	 * @param  string $active_tab currently active tab (a settings page id).
	 */
	public function options_tabs( $active_tab = '' ) {
		$tabs = [];
		$tabs = apply_filters( 'twsc_options_tabs', $tabs );
		if ( is_array( $tabs ) && count( $tabs ) > 1 ) {
			echo '<p class="nav-tab-wrapper">';
			foreach ( $tabs as $tab ) {
				$classes  = 'nav-tab';
				$classes  = $active_tab === $tab[0] ? $classes . ' nav-tab-active' : $classes;
				$page_var = '?page=' . $tab[0];
				?>
				<a href="<?php echo esc_url( $page_var ); ?>" class="<?php echo esc_attr( $classes ); ?>"><?php echo esc_html( $tab[1] ); ?></a>
				<?php
			}
			echo '</p>';
		}
	}
}
