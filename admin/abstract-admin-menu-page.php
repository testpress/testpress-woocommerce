
<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Testpress_Lms
 * @subpackage Testpress_Lms/admin
 * @author     Testpress <support@testpress.in>
 */


abstract class AbstractMenuPage {

	protected $order;
	protected $name;
	protected $screenId;
	protected $parentMenu;
	protected $pageTitle;
	protected $menuTitle;
	protected $capability;
	protected $menuSlug;

	public function __construct( $name, $atts = array() ) {

		parent::__construct();

		$this->name = $name;

		$defaults = array(
			'order'      => 30,
			'page_title' => $this->name,
			'menu_title' => $this->name,
			'capability' => 'manage_options',
			'parent_menu' => null
		);

		$atts = array_merge( $defaults, $atts );

		$this->order      = $atts['order'];
		$this->parentMenu = $atts['parent_menu'];
		$this->pageTitle  = $atts['page_title'];
		$this->menuTitle  = $atts['menu_title'];
		$this->capability = $atts['capability'];

		$this->addActions();
	}


	public function addActions() {
		add_action( 'admin_menu', array( $this, 'createMenu' ), $this->order );
	}


	public function createMenu() {
		if ( $this->parentMenu ) {
			$hook = add_submenu_page( $this->parentMenu, $this->getPageTitle(), $this->getMenuTitle(), $this->capability, $this->name, array(
				$this,
				'render'
			) );
		} else {
			$hook = add_menu_page( "Testpress LMS","Testpress LMS", $this->capability, $this->name, array(
				$this,
				'render'
			), 'dashicons-clipboard', $this->order );
			$hook = add_submenu_page( $this->name, $this->getPageTitle(), $this->getMenuTitle(), $this->capability, $this->name, array(
				$this,
				'render'
			) );
		}
		$this->screenId = $hook;
		add_action( 'load-' . $this->screenId, array( $this, 'onLoad' ) );
	}

	abstract public function onLoad();

	abstract public function render();

	public function getName() {
		return $this->name;
	}

	public function getUrl( $additionalArgs = array() ) {

		$defaultArgs = array(
			'page' => $this->name
		);

		$args = array_merge( $defaultArgs, $additionalArgs );

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	abstract protected function getPageTitle();

	abstract protected function getMenuTitle();

    
	/**
	 * @note use function after admin_init hook (current_screen for ex.)
	 *
	 * @param array $atts
	 *
	 * @return boolean
	 */
	public function isCurrentPage( $atts = array() ) {

		if ( ! is_admin() ) {
			return false;
		}

		$currentScreen = get_current_screen();

		if ( $currentScreen->id !== $this->screenId ) {
			return false;
		}

		return true;
	}
