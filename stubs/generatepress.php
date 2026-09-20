<?php
/**
 * PHPStan-Stubs: generatepress 3.6.1, erzeugt am 12.09.2026 mit php-stubs/generator
 * aus themes/generatepress der Site staging.ofenbau-schwarzer.local.
 * Nur für die statische Analyse, wird nie von WordPress geladen.
 * Neu erzeugen: ~/.claude/skills/_werkzeuge/stubs-generieren.sh
 */

/**
 * Creates minified css via PHP.
 *
 * @author  Carlos Rios
 * Modified by Tom Usborne for GeneratePress
 */
class GeneratePress_CSS
{
    /**
     * The css selector that you're currently adding rules to
     *
     * @access protected
     * @var string
     */
    protected $_selector = '';
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    /**
     * Stores the final css output with all of its rules for the current selector.
     *
     * @access protected
     * @var string
     */
    protected $_selector_output = '';
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    /**
     * Stores all of the rules that will be added to the selector
     *
     * @access protected
     * @var string
     */
    protected $_css = '';
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    /**
     * The string that holds all of the css to output
     *
     * @access protected
     * @var string
     */
    protected $_output = '';
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    /**
     * Stores media queries
     *
     * @var null
     */
    protected $_media_query = \null;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    /**
     * The string that holds all of the css to output inside of the media query
     *
     * @access protected
     * @var string
     */
    protected $_media_query_output = '';
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    /**
     * Sets a selector to the object and changes the current selector to a new one
     *
     * @access public
     * @since  1.0
     *
     * @param  string $selector - the css identifier of the html that you wish to target.
     * @return $this
     */
    public function set_selector($selector = '')
    {
    }
    /**
     * Adds a css property with value to the css output
     *
     * @access public
     * @since  1.0
     *
     * @param string $property The css property.
     * @param string $value The value to be placed with the property.
     * @param string $og_default Check to see if the value matches the default.
     * @param string $unit The unit for the value (px).
     * @return $this
     */
    public function add_property($property, $value, $og_default = \false, $unit = \false)
    {
    }
    /**
     * Sets a media query in the class
     *
     * @since  1.1
     * @param string $value The media query.
     * @return $this
     */
    public function start_media_query($value)
    {
    }
    /**
     * Stops using a media query.
     *
     * @see    start_media_query()
     *
     * @since  1.1
     * @return $this
     */
    public function stop_media_query()
    {
    }
    /**
     * Returns the minified css in the $_output variable
     *
     * @access public
     * @since  1.0
     *
     * @return string
     */
    public function css_output()
    {
    }
}
/**
 * This class adds HTML attributes to various theme elements.
 */
class GeneratePress_Dashboard
{
    /**
     * Initiator
     */
    public static function get_instance()
    {
    }
    /**
     * Get started.
     */
    public function __construct()
    {
    }
    /**
     * Add our dashboard menu item.
     */
    public function add_menu_item()
    {
    }
    /**
     * Get our dashboard pages so we can style them.
     */
    public static function get_pages()
    {
    }
    /**
     * Add a body class on GP dashboard pages.
     *
     * @param string $classes The existing classes.
     */
    public function set_admin_body_class($classes)
    {
    }
    /**
     * Build our Dashboard header.
     */
    public static function header()
    {
    }
    /**
     * Build our Dashboard menu.
     */
    public static function navigation()
    {
    }
    /**
     * Add our Dashboard headers.
     */
    public function add_header()
    {
    }
    /**
     * Add our scripts to the page.
     */
    public function enqueue_scripts()
    {
    }
    /**
     * Add the HTML for our page.
     */
    public function page()
    {
    }
    /**
     * Add the container for our start customizing app.
     */
    public function start_customizing()
    {
    }
    /**
     * Add the container for our start customizing app.
     */
    public function go_pro()
    {
    }
    /**
     * Add the container for our reset app.
     */
    public function reset()
    {
    }
}
/**
 * This class adds HTML attributes to various theme elements.
 */
class GeneratePress_HTML_Attributes
{
    /**
     * Initiator
     */
    public static function get_instance()
    {
    }
    /**
     *  Constructor
     */
    public function __construct()
    {
    }
    /**
     * Parse the attributes.
     *
     * @since 3.1.0
     * @param array  $attributes The current attributes.
     * @param string $context The context in which attributes are applied.
     * @param array  $settings Custom settings passed to the filter.
     */
    public function parse_attributes($attributes, $context, $settings)
    {
    }
    /**
     * Add attributes to our top bar.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function top_bar($attributes)
    {
    }
    /**
     * Add attributes to our inside top bar container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function inside_top_bar($attributes)
    {
    }
    /**
     * Add attributes to our site header.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function site_header($attributes)
    {
    }
    /**
     * Add attributes to our inside site header container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function inside_site_header($attributes)
    {
    }
    /**
     * Add attributes to our menu toggle.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function menu_toggle($attributes)
    {
    }
    /**
     * Add attributes to our main navigation.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function primary_navigation($attributes)
    {
    }
    /**
     * Add attributes to our main navigation.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function primary_inner_navigation($attributes)
    {
    }
    /**
     * Add attributes to our main navigation.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function mobile_menu_control_wrapper($attributes)
    {
    }
    /**
     * Add attributes to our footer element.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function site_info($attributes)
    {
    }
    /**
     * Add attributes to our inside site info container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function inside_site_info($attributes)
    {
    }
    /**
     * Add attributes to our entry headers.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function entry_header($attributes)
    {
    }
    /**
     * Add attributes to our page headers.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function page_header($attributes)
    {
    }
    /**
     * Add attributes to our entry headers.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function post_navigation($attributes)
    {
    }
    /**
     * Add attributes to our page container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function page($attributes)
    {
    }
    /**
     * Add attributes to our site content container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function site_content($attributes)
    {
    }
    /**
     * Add attributes to our primary content container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function content($attributes)
    {
    }
    /**
     * Add attributes to our primary content container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function main($attributes)
    {
    }
    /**
     * Add attributes to our left sidebar.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function left_sidebar($attributes)
    {
    }
    /**
     * Add attributes to our right sidebar.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function right_sidebar($attributes)
    {
    }
    /**
     * Add attributes to our footer widget inner container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function footer_widgets_container($attributes)
    {
    }
    /**
     * Add attributes to our footer widget inner container.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     * @param array $settings Settings passed through the function.
     */
    public function comment_body($attributes, $settings)
    {
    }
    /**
     * Add attributes to our comment meta.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function comment_meta($attributes)
    {
    }
    /**
     * Add attributes to our footer entry meta.
     *
     * @since 3.1.0
     * @param array $attributes The existing attributes.
     */
    public function footer_entry_meta($attributes)
    {
    }
    /**
     * Add attributes to our WooCommerce content container.
     *
     * @since 3.2.0
     * @param array $attributes The existing attributes.
     */
    public function woocommerce_content($attributes)
    {
    }
}
/**
 * Class GenerateBlocks_Rest
 */
class GeneratePress_Rest extends \WP_REST_Controller
{
    /**
     * Namespace.
     *
     * @var string
     */
    protected $namespace = 'generatepress/v';
    /**
     * Version.
     *
     * @var string
     */
    protected $version = '1';
    /**
     * Initiator.
     *
     * @return object initialized object of class.
     */
    public static function get_instance()
    {
    }
    /**
     * GeneratePress_Rest constructor.
     */
    public function __construct()
    {
    }
    /**
     * Register rest routes.
     */
    public function register_routes()
    {
    }
    /**
     * Get edit options permissions.
     *
     * @return bool
     */
    public function update_settings_permission()
    {
    }
    /**
     * Reset settings.
     *
     * @param WP_REST_Request $request request object.
     *
     * @return mixed
     */
    public function reset(\WP_REST_Request $request)
    {
    }
    /**
     * Success rest.
     *
     * @param mixed $response response data.
     * @return mixed
     */
    public function success($response)
    {
    }
    /**
     * Failed rest.
     *
     * @param mixed $response response data.
     * @return mixed
     */
    public function failed($response)
    {
    }
    /**
     * Error rest.
     *
     * @param mixed $code     error code.
     * @param mixed $response response data.
     * @return mixed
     */
    public function error($code, $response)
    {
    }
}
/**
 * Process option updates if necessary.
 */
class GeneratePress_Theme_Update
{
    /**
     * Initiator
     */
    public static function get_instance()
    {
    }
    /**
     *  Constructor
     */
    public function __construct()
    {
    }
    /**
     * Implement theme update logic. Only run updates on existing sites.
     *
     * @since 3.0.0
     */
    public static function init()
    {
    }
    /**
     * Less important updates that should only happen in the Dashboard.
     * These use a database flag instead of our version number for legacy reasons.
     *
     * @since 3.0.0
     */
    public static function admin_updates()
    {
    }
    /**
     * Remove variants from font family values.
     *
     * @since 1.3.0
     */
    public static function v_1_3_0()
    {
    }
    /**
     * Move logo to custom_logo option as required by WP.org.
     *
     * @since 1.3.29
     */
    public static function v_1_3_29()
    {
    }
    /**
     * Turn off the combine CSS option for existing sites.
     *
     * @since 2.3.0
     */
    public static function v_2_3_0()
    {
    }
    /**
     * Update sites using old defaults.
     *
     * @since 3.0.0
     */
    public static function v_3_0_0()
    {
    }
    /**
     * Update sites using old defaults.
     *
     * @since 3.1.0
     */
    public static function v_3_1_0()
    {
    }
}
/**
 * Handles all of our typography migration.
 */
class GeneratePress_Typography_Migration
{
    /**
     * Initiator
     */
    public static function get_instance()
    {
    }
    /**
     * Map our new typography keys to the old prefixes.
     */
    public static function get_option_prefixes()
    {
    }
    /**
     * Check if we have a saved value.
     *
     * @param string $id The option ID.
     * @param array  $settings The saved settings.
     * @param array  $defaults The defaults.
     */
    public static function has_saved_value($id, $settings, $defaults)
    {
    }
    /**
     * Get all of our mapped typography data.
     */
    public static function get_mapped_typography_data()
    {
    }
    /**
     * Get all of our mapped font data.
     */
    public static function get_mapped_font_data()
    {
    }
}
/**
 * Handles all of our typography option output.
 */
class GeneratePress_Typography
{
    /**
     * Initiator
     */
    public static function get_instance()
    {
    }
    /**
     *  Constructor
     */
    public function __construct()
    {
    }
    /**
     * Generate our Google Fonts URI.
     */
    public static function get_google_fonts_uri()
    {
    }
    /**
     * Enqueue Google Fonts if they're set.
     */
    public function enqueue_google_fonts()
    {
    }
    /**
     * Build our typography CSS.
     *
     * @param string $module            The name of the module we're generating CSS for.
     * @param string $specific_selector Target a specific selector to get the CSS for.
     */
    public static function get_css($module = 'core', $specific_selector = '')
    {
    }
    /**
     * Get the CSS selector.
     *
     * @param string $selector The saved selector to look up.
     */
    public static function get_css_selector($selector)
    {
    }
    /**
     * Get our full font family value.
     *
     * @param string $font_family The font family name.
     */
    public static function get_font_family($font_family)
    {
    }
    /**
     * Get the defaults for our CSS options.
     */
    public static function get_defaults()
    {
    }
    /**
     * Add editor styles to the block editor.
     *
     * @param array $editor_styles Existing styles.
     */
    public function add_editor_styles($editor_styles)
    {
    }
    /**
     * Add scripts to the block editor.
     */
    public function enqueue_editor_scripts()
    {
    }
}
/**
 * Helper functions to add Customizer fields.
 */
class GeneratePress_Customize_Field
{
    /**
     * Initiator.
     *
     * @since 1.2.0
     * @return object initialized object of class.
     */
    public static function get_instance()
    {
    }
    /**
     * Add a wrapper for defined controls.
     *
     * @param string $id The settings ID for this field.
     * @param array  $control_args The args for add_control().
     */
    public static function add_wrapper($id, $control_args = array())
    {
    }
    /**
     * Add a title.
     *
     * @param string $id The settings ID for this field.
     * @param array  $control_args The args for add_control().
     */
    public static function add_title($id, $control_args = array())
    {
    }
    /**
     * Add a Customizer field.
     *
     * @param string $id The settings ID for this field.
     * @param object $control_class A custom control classes if we want one.
     * @param array  $setting_args The args for add_setting().
     * @param array  $control_args The args for add_control().
     */
    public static function add_field($id, $control_class, $setting_args = array(), $control_args = array())
    {
    }
    /**
     * Add color field group.
     *
     * @param string $id The ID for the group wrapper.
     * @param string $section_id The section ID.
     * @param string $toggle_id The Toggle ID.
     * @param array  $fields The color fields.
     */
    public static function add_color_field_group($id, $section_id, $toggle_id, $fields)
    {
    }
}
/**
 * Customize API: ColorAlpha class
 *
 * @package GeneratePress
 */
/**
 * Customize Color Control class.
 *
 * @since 1.0.0
 *
 * @see WP_Customize_Control
 */
class GeneratePress_Customize_Color_Control extends \WP_Customize_Color_Control
{
    /**
     * Type.
     *
     * @access public
     * @since 1.0.0
     * @var string
     */
    public $type = 'generate-color-control';
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::to_json()
     */
    public function to_json()
    {
    }
    /**
     * Empty JS template.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function content_template()
    {
    }
}
/**
 * Create our container width slider control
 *
 * @deprecated 1.3.47
 */
class Generate_Customize_Width_Slider_Control extends \WP_Customize_Control
{
    /**
     * Render content.
     */
    public function render_content()
    {
    }
}
/**
 * Heading area
 *
 * @since 0.1
 * @depreceted 1.3.41
 **/
class GenerateLabelControl extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Render content.
     */
    public function render_content()
    {
    }
}
/**
 * A class to create a dropdown for all google fonts
 */
class Generate_Google_Font_Dropdown_Custom_Control extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Set type.
     *
     * @var $type
     */
    public $type = 'gp-customizer-fonts';
    /**
     * Enqueue scripts.
     */
    public function enqueue()
    {
    }
    /**
     * Send variables to json.
     */
    public function to_json()
    {
    }
    /**
     * Render content.
     */
    public function content_template()
    {
    }
}
/**
 * A class to create a dropdown for font weight
 */
class Generate_Select_Control extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Set type.
     *
     * @var $type
     */
    public $type = 'gp-typography-select';
    /**
     * Set choices.
     *
     * @var $choices
     */
    public $choices = array();
    /**
     * Send variables to json.
     */
    public function to_json()
    {
    }
    /**
     * Render content.
     */
    public function content_template()
    {
    }
}
/**
 * Create our hidden input control
 */
class Generate_Hidden_Input_Control extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Set type.
     *
     * @var $type
     */
    public $type = 'gp-hidden-input';
    /**
     * Set ID
     *
     * @var $id
     */
    public $id = '';
    /**
     * Send variables to json.
     */
    public function to_json()
    {
    }
    /**
     * Render content.
     */
    public function content_template()
    {
    }
}
/**
 * A class to create a dropdown for font weight
 *
 * @deprecated since 1.3.40
 */
class Generate_Font_Weight_Custom_Control extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Construct.
     *
     * @param object $manager The manager.
     * @param int    $id The ID.
     * @param array  $args The args.
     * @param array  $options The options.
     */
    public function __construct($manager, $id, $args = array(), $options = array())
    {
    }
    /**
     * Render the content of the category dropdown
     */
    public function render_content()
    {
    }
}
/**
 * A class to create a dropdown for text-transform
 *
 * @deprecated since 1.3.40
 */
class Generate_Text_Transform_Custom_Control extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Construct.
     *
     * @param object $manager The manager.
     * @param int    $id The ID.
     * @param array  $args The args.
     * @param array  $options The options.
     */
    public function __construct($manager, $id, $args = array(), $options = array())
    {
    }
    /**
     * Render the content of the category dropdown
     */
    public function render_content()
    {
    }
}
/**
 * Create our container width slider control
 *
 * @deprecated 1.3.47
 */
class Generate_Customize_Slider_Control extends \WP_Customize_Control
{
    // phpcs:ignore
    /**
     * Render content.
     */
    public function render_content()
    {
    }
}
/**
 * Create a range slider control.
 * This control allows you to add responsive settings.
 *
 * @since 1.3.47
 */
class Generate_Range_Slider_Control extends \WP_Customize_Control
{
    /**
     * The control type.
     *
     * @access public
     * @var string
     */
    public $type = 'generatepress-range-slider';
    /**
     * The control description.
     *
     * @access public
     * @var string
     */
    public $description = '';
    /**
     * The control sub-description.
     *
     * @access public
     * @var string
     */
    public $sub_description = '';
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @see WP_Customize_Control::to_json()
     */
    public function to_json()
    {
    }
    /**
     * Enqueue control related scripts/styles.
     *
     * @access public
     */
    public function enqueue()
    {
    }
    /**
     * An Underscore (JS) template for this control's content (but not its container).
     *
     * Class variables for this control class are available in the `data` JS object;
     * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
     *
     * @see WP_Customize_Control::print_template()
     *
     * @access protected
     */
    protected function content_template()
    {
    }
}
/**
 * Customize API: ColorAlpha class
 *
 * @package GeneratePress
 */
/**
 * Customize Color Control class.
 *
 * @since 1.0.0
 *
 * @see WP_Customize_Control
 */
class GeneratePress_Customize_React_Control extends \WP_Customize_Control
{
    /**
     * Type.
     *
     * @access public
     * @since 1.0.0
     * @var string
     */
    public $type = 'generate-react-control';
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::to_json()
     */
    public function to_json()
    {
    }
    /**
     * Empty JS template.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function content_template()
    {
    }
    /**
     * Empty PHP template.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function render_content()
    {
    }
}
/**
 * Create the typography elements control.
 *
 * @since 2.0
 */
class Generate_Typography_Customize_Control extends \WP_Customize_Control
{
    /**
     * Set the type.
     *
     * @var string $type
     */
    public $type = 'gp-customizer-typography';
    /**
     * Enqueue scripts.
     */
    public function enqueue()
    {
    }
    /**
     * Send variables to json.
     */
    public function to_json()
    {
    }
    /**
     * Render content.
     */
    public function content_template()
    {
    }
    /**
     * Build font weight choices.
     */
    public function get_font_weight_choices()
    {
    }
    /**
     * Build text transform choices.
     */
    public function get_font_transform_choices()
    {
    }
}
/**
 * Create our in-section upsell controls.
 * Escape your URL in the Customizer using esc_url().
 *
 * @since 0.1
 */
class Generate_Customize_Misc_Control extends \WP_Customize_Control
{
    /**
     * Set description.
     *
     * @var public $description
     */
    public $description = '';
    /**
     * Set URL.
     *
     * @var public $url
     */
    public $url = '';
    /**
     * Set type.
     *
     * @var public $type
     */
    public $type = 'addon';
    /**
     * Set label.
     *
     * @var public $label
     */
    public $label = '';
    /**
     * Enqueue scripts.
     */
    public function enqueue()
    {
    }
    /**
     * Send variables to json.
     */
    public function to_json()
    {
    }
    /**
     * Render content.
     */
    public function content_template()
    {
    }
}
/**
 * Create our upsell section.
 * Escape your URL in the Customizer using esc_url().
 *
 * @since unknown
 */
class GeneratePress_Upsell_Section extends \WP_Customize_Section
{
    /**
     * Set type.
     *
     * @var public $type
     */
    public $type = 'gp-upsell-section';
    /**
     * Set pro URL.
     *
     * @var public $pro_url
     */
    public $pro_url = '';
    /**
     * Set pro text.
     *
     * @var public $pro_text
     */
    public $pro_text = '';
    /**
     * Set ID.
     *
     * @var public $id
     */
    public $id = '';
    /**
     * Send variables to json.
     */
    public function json()
    {
    }
    /**
     * Render content.
     */
    protected function render_template()
    {
    }
}
/**
 * Customize API: Wrapper class.
 *
 * @package GeneratePress
 */
/**
 * Customize Wrapper Control class.
 *
 * @see WP_Customize_Control
 */
class GeneratePress_Customize_Wrapper_Control extends \WP_Customize_Control
{
    /**
     * Type.
     *
     * @access public
     * @since 1.0.0
     * @var string
     */
    public $type = 'generate-wrapper-control';
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::to_json()
     */
    public function to_json()
    {
    }
    /**
     * Empty JS template.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function content_template()
    {
    }
    /**
     * Empty PHP template.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function render_content()
    {
    }
    /**
     * Add a script to toggle the wrapper.
     */
    public function toggleIdScript()
    {
    }
}
/**
 * Add current-menu-item to the current item if no theme location is set
 * This means we don't have to duplicate CSS properties for current_page_item and current-menu-item
 *
 * @since 1.3.21
 */
class Generate_Page_Walker extends \Walker_Page
{
    function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0)
    {
    }
}
// Set our theme version.
\define('GENERATE_VERSION', '3.6.1');
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 0.1
 */
function generate_setup()
{
}
/**
 * Check what sidebar layout we're using.
 * We need this function as the post meta in generate_get_layout() only runs
 * on is_singular()
 *
 * @since 2.2
 *
 * @param bool $meta Check for post meta.
 * @return string The saved sidebar layout.
 */
function generate_get_block_editor_sidebar_layout($meta = \true)
{
}
/**
 * Check whether we're disabling the content title or not.
 * We need this function as the post meta in generate_show_title() only runs
 * on is_singular()
 *
 * @since 2.2
 */
function generate_get_block_editor_show_content_title()
{
}
/**
 * Get the content width for this post.
 *
 * @since 2.2
 */
function generate_get_block_editor_content_width()
{
}
/**
 * Add dynamic inline styles to the block editor content.
 *
 * @param array $editor_settings The existing editor settings.
 */
function generate_add_inline_block_editor_styles($editor_settings)
{
}
/**
 * Add CSS to the admin side of the block editor.
 *
 * @since 2.2
 */
function generate_enqueue_backend_block_editor_assets()
{
}
/**
 * Write our CSS for the block editor.
 *
 * @since 2.2
 * @param string $for Define whether this CSS for the block content or the block editor.
 */
function generate_do_inline_block_editor_css($for = 'block-content')
{
}
/**
 * Generate the CSS in the <head> section using the Theme Customizer.
 *
 * @since 0.1
 */
function generate_base_css()
{
}
/**
 * Generate the CSS in the <head> section using the Theme Customizer.
 *
 * @since 0.1
 */
function generate_advanced_css()
{
}
/**
 * Generate the CSS in the <head> section using the Theme Customizer.
 *
 * @since 0.1
 */
function generate_font_css()
{
}
/**
 * Write our dynamic CSS.
 *
 * @since 0.1
 */
function generate_spacing_css()
{
}
/**
 * Generates any CSS that can't be cached (can change from page to page).
 *
 * @since 2.0
 */
function generate_no_cache_dynamic_css()
{
}
/**
 * Get all of our dynamic CSS to be cached/added to a file.
 *
 * @since 3.0.0
 */
function generate_get_dynamic_css()
{
}
/**
 * Enqueue our dynamic CSS.
 *
 * @since 2.0
 */
function generate_enqueue_dynamic_css()
{
}
/**
 * Sets our dynamic CSS cache if it doesn't exist.
 *
 * If the theme version changed, bust the cache.
 *
 * @since 2.0
 */
function generate_set_dynamic_css_cache()
{
}
/**
 * Update our CSS cache when done saving Customizer options.
 *
 * @since 2.0
 */
function generate_update_dynamic_css_cache()
{
}
/**
 * Do the modal CSS.
 *
 * @param Object $css The existing CSS object.
 */
function generate_do_modal_css($css)
{
}
/**
 * Set up helpers early so they're always available.
 * Other modules might need access to them at some point.
 *
 * @since 2.0
 */
function generate_set_customizer_helpers()
{
}
/**
 * Add our base options to the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function generate_customize_register($wp_customize)
{
}
/**
 * Add CSS for our controls
 *
 * @since 1.3.41
 */
function generate_customizer_controls_css()
{
}
/**
 * Sanitize typography dropdown.
 *
 * @since 1.1.10
 * @deprecated 1.3.45
 * @param string $input The value to check.
 */
function generate_sanitize_typography($input)
{
}
/**
 * Sanitize font weight.
 *
 * @since 1.1.10
 * @deprecated 1.3.40
 * @param string $input The value to check.
 */
function generate_sanitize_font_weight($input)
{
}
/**
 * Sanitize text transform.
 *
 * @since 1.1.10
 * @deprecated 1.3.40
 * @param string $input The value to check.
 */
function generate_sanitize_text_transform($input)
{
}
/**
 * Hide the hidden input control
 *
 * @since 1.3.40
 */
function generate_typography_customize_preview_css()
{
}
/**
 * Adds a hidden navigation if no navigation is set
 * This allows us to use postMessage to position the navigation when it doesn't exist
 *
 * @since 1.3.40
 */
function generate_hidden_navigation()
{
}
/**
 * Check to see if we're on a posts page
 *
 * @since 1.3.39
 */
function generate_is_posts_page()
{
}
/**
 * Check to see if we're using our footer bar widget
 *
 * @since 1.3.42
 */
function generate_is_footer_bar_active()
{
}
/**
 * Check to see if the top bar is active
 *
 * @since 1.3.45
 */
function generate_is_top_bar_active()
{
}
/**
 * Render the site title for the selective refresh partial.
 *
 * @since 1.3.41
 */
function generate_customize_partial_blogname()
{
}
/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since 1.3.41
 */
function generate_customize_partial_blogdescription()
{
}
/**
 * Add our custom color palettes to the color pickers in the Customizer.
 *
 * @since 1.3.42
 */
function generate_enqueue_color_palettes()
{
}
/**
 * Sanitize integers.
 *
 * @since 1.0.8
 * @param string $input The value to check.
 */
function generate_sanitize_integer($input)
{
}
/**
 * Sanitize integers that can use decimals.
 *
 * @since 1.3.41
 * @param string $input The value to check.
 */
function generate_sanitize_decimal_integer($input)
{
}
/**
 * Sanitize integers that can use decimals.
 *
 * @since 3.1.0
 * @param string $input The value to check.
 */
function generate_sanitize_empty_decimal_integer($input)
{
}
/**
 * Sanitize integers that can use negative decimals.
 *
 * @since 3.1.0
 * @param string $input The value to check.
 */
function generate_sanitize_empty_negative_decimal_integer($input)
{
}
/**
 * Sanitize a positive number, but allow an empty value.
 *
 * @since 2.2
 * @param string $input The value to check.
 */
function generate_sanitize_empty_absint($input)
{
}
/**
 * Sanitize checkbox values.
 *
 * @since 1.0.8
 * @param string $checked The value to check.
 */
function generate_sanitize_checkbox($checked)
{
}
/**
 * Sanitize blog excerpt.
 * Needed because GP Premium calls the control ID which is different from the settings ID.
 *
 * @since 1.0.8
 * @param string $input The value to check.
 */
function generate_sanitize_blog_excerpt($input)
{
}
/**
 * Sanitize colors.
 * Allow blank value.
 *
 * @since 1.2.9.6
 * @param string $color The color to check.
 */
function generate_sanitize_hex_color($color)
{
}
/**
 * Sanitize RGBA colors.
 *
 * @since 2.2
 * @param string $color The color to check.
 */
function generate_sanitize_rgba_color($color)
{
}
/**
 * Sanitize choices.
 *
 * @since 1.3.24
 * @param string $input The value to check.
 * @param object $setting The setting object.
 */
function generate_sanitize_choices($input, $setting)
{
}
/**
 * Sanitize our Google Font variants
 *
 * @since 2.0
 * @param string $input The value to check.
 */
function generate_sanitize_variants($input)
{
}
/**
 * Add misc inline scripts to our controls.
 *
 * We don't want to add these to the controls themselves, as they will be repeated
 * each time the control is initialized.
 *
 * @since 2.0
 */
function generate_do_control_inline_scripts()
{
}
/**
 * Add our live preview scripts
 *
 * @since 0.1
 */
function generate_customizer_live_preview()
{
}
/**
 * Check to see if we have a logo or not.
 *
 * Used as an active callback. Calling has_custom_logo creates a PHP notice for
 * multisite users.
 *
 * @since 2.0.1
 */
function generate_has_custom_logo_callback()
{
}
/**
 * Save our preset layout controls. These should always save to be "current".
 *
 * @since 2.2
 */
function generate_sanitize_preset_layout()
{
}
/**
 * Display options if we're using the Floats structure.
 */
function generate_is_using_floats_callback()
{
}
/**
 * Callback to determine whether to show the inline logo option.
 */
function generate_show_inline_logo_callback()
{
}
/**
 * Adds our "GeneratePress" dashboard menu item
 *
 * @since 0.1
 */
function generate_create_menu()
{
}
/**
 * Adds any necessary scripts to the GP dashboard page
 *
 * @since 0.1
 */
function generate_options_styles()
{
}
/**
 * Builds the content of our GP dashboard page
 *
 * @since 0.1
 */
function generate_settings_page()
{
}
/**
 * Reset customizer settings
 *
 * @since 0.1
 */
function generate_reset_customizer_settings()
{
}
/**
 * Add our admin notices
 *
 * @since 0.1
 */
function generate_admin_errors()
{
}
/**
 * Set default options
 *
 * @since 0.1
 */
function generate_get_defaults()
{
}
/**
 * Set default options
 */
function generate_get_color_defaults()
{
}
/**
 * Set default options.
 *
 * @since 0.1
 *
 * @param bool $filter Whether to return the filtered values or original values.
 * @return array Option defaults.
 */
function generate_get_default_fonts($filter = \true)
{
}
/**
 * Set the default options.
 *
 * @since 0.1
 *
 * @param bool $filter Whether to return the filtered values or original values.
 * @return array Option defaults.
 */
function generate_spacing_get_defaults($filter = \true)
{
}
/**
 * Set the default system fonts.
 *
 * @since 1.3.40
 */
function generate_typography_default_fonts()
{
}
// Deprecated constants.
\define('GENERATE_URI', \get_template_directory_uri());
\define('GENERATE_DIR', \get_template_directory());
/**
 * Build the pagination links
 *
 * @since 1.3.35
 * @deprecated 1.3.45
 */
function generate_paging_nav()
{
}
/**
 * Add fallback CSS for our mobile search icon color
 *
 * @deprecated 1.3.47
 */
function generate_additional_spacing()
{
}
/**
 * Enqueue our mobile search icon color fallback CSS
 *
 * @deprecated 1.3.47
 */
function generate_mobile_search_spacing_fallback_css()
{
}
/**
 * Check to see if there's any addons not already activated
 *
 * @since 1.0.9
 * @deprecated 1.3.47
 */
function generate_addons_available()
{
}
/**
 * Check to see if no addons are activated
 *
 * @since 1.0.9
 * @deprecated 1.3.47
 */
function generate_no_addons()
{
}
/**
 * Figure out if we should use minified scripts or not
 *
 * @since 1.3.29
 * @deprecated 2.0
 */
function generate_get_min_suffix()
{
}
/**
 * Add layout metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_add_layout_meta_box()
{
}
/**
 * Show layout metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_show_layout_meta_box()
{
}
/**
 * Save layout metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_save_layout_meta()
{
}
/**
 * Add footer widget metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_add_footer_widget_meta_box()
{
}
/**
 * Show footer widget metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_show_footer_widget_meta_box()
{
}
/**
 * Save footer widget metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_save_footer_widget_meta()
{
}
/**
 * Add page builder metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_add_page_builder_meta_box()
{
}
/**
 * Show page builder metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_show_page_builder_meta_box()
{
}
/**
 * Save page builder metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_save_page_builder_meta()
{
}
/**
 * Add disable elements metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_add_de_meta_box()
{
}
/**
 * Show disable elements metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_show_de_meta_box()
{
}
/**
 * Save disable elements metabox.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_save_de_meta()
{
}
/**
 * Add base inline CSS.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_add_base_inline_css()
{
}
/**
 * Enqueue base colors inline CSS.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_color_scripts()
{
}
/**
 * Enqueue typography CSS.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_typography_scripts()
{
}
/**
 * Enqueue spacing CSS.
 *
 * @since 0.1
 * @deprecated 2.0
 */
function generate_spacing_scripts()
{
}
/**
 * A wrapper function to get our settings.
 *
 * @since 1.3.40
 *
 * @param string $setting The option name to look up.
 * @return string The option value.
 * @todo Ability to specify different option name and defaults.
 */
function generate_get_setting($setting)
{
}
/**
 * Display the classes for the sidebar.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_right_sidebar_class($class = '')
{
}
/**
 * Retrieve the classes for the sidebar.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_right_sidebar_class($class = '')
{
}
/**
 * Display the classes for the sidebar.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_left_sidebar_class($class = '')
{
}
/**
 * Retrieve the classes for the sidebar.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_left_sidebar_class($class = '')
{
}
/**
 * Display the classes for the content.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_content_class($class = '')
{
}
/**
 * Retrieve the classes for the content.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_content_class($class = '')
{
}
/**
 * Display the classes for the header.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_header_class($class = '')
{
}
/**
 * Retrieve the classes for the content.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_header_class($class = '')
{
}
/**
 * Display the classes for inside the header.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_inside_header_class($class = '')
{
}
/**
 * Retrieve the classes for inside the header.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_inside_header_class($class = '')
{
}
/**
 * Display the classes for the container.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_container_class($class = '')
{
}
/**
 * Retrieve the classes for the content.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_container_class($class = '')
{
}
/**
 * Display the classes for the navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_navigation_class($class = '')
{
}
/**
 * Retrieve the classes for the navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_navigation_class($class = '')
{
}
/**
 * Display the classes for the inner navigation.
 *
 * @since 1.3.41
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_inside_navigation_class($class = '')
{
}
/**
 * Display the classes for the navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_menu_class($class = '')
{
}
/**
 * Retrieve the classes for the navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_menu_class($class = '')
{
}
/**
 * Display the classes for the <main> container.
 *
 * @since 1.1.0
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_main_class($class = '')
{
}
/**
 * Retrieve the classes for the footer.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_main_class($class = '')
{
}
/**
 * Display the classes for the footer.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_footer_class($class = '')
{
}
/**
 * Retrieve the classes for the footer.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_footer_class($class = '')
{
}
/**
 * Display the classes for the footer.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_inside_footer_class($class = '')
{
}
/**
 * Display the classes for the top bar.
 *
 * @since 1.3.45
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_top_bar_class($class = '')
{
}
/**
 * Figure out which schema tags to apply to the <body> element.
 *
 * @since 1.3.15
 */
function generate_body_schema()
{
}
/**
 * Figure out which schema tags to apply to the <article> element
 * The function determines the itemtype: generate_article_schema( 'BlogPosting' )
 *
 * @since 1.3.15
 * @param string $type The type of schema.
 */
function generate_article_schema($type = 'CreativeWork')
{
}
/**
 * Process database updates if necessary.
 * There's nothing in here yet, but we're setting the version to use later.
 *
 * @since 2.1
 * @deprecated 3.0.0
 */
function generate_do_admin_db_updates()
{
}
/**
 * Process important database updates when someone visits the front or backend.
 *
 * @since 2.3
 * @deprecated 3.0.0
 */
function generate_do_db_updates()
{
}
/**
 * Migrate the old logo database entry to the new custom_logo theme mod (WordPress 4.5)
 *
 * @since 1.3.29
 * @deprecated 3.0.0
 */
function generate_update_logo_setting()
{
}
/**
 * Take the old body font value and strip it of variants
 * This should only run once
 *
 * @since 1.3.0
 * @deprecated 3.0.0
 */
function generate_typography_convert_values()
{
}
/**
 * Execute functions after existing sites update.
 *
 * We check to see if options already exist. If they do, we can assume the user has
 * updated the theme, and not installed it from scratch.
 *
 * We run this right away in the Dashboard to avoid other migration functions from
 * setting options and causing these functions to run on fresh installs.
 *
 * @since 2.0
 * @deprecated 3.0.0
 */
function generate_migrate_existing_settings()
{
}
/**
 * Output CSS for the icon fonts.
 *
 * @since 2.3
 * @deprecated 3.0.0
 */
function generate_do_icon_css()
{
}
/**
 * Enqueue scripts and styles
 */
function generate_scripts()
{
}
/**
 * Register widgetized area and update sidebar with default widgets
 */
function generate_widgets_init()
{
}
/**
 * Set the $content_width depending on layout of current page
 * Hook into "wp" so we have the correct layout setting from generate_get_layout()
 * Hooking into "after_setup_theme" doesn't get the correct layout setting
 */
function generate_smart_content_width()
{
}
/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since 0.1
 *
 * @param array $args The existing menu args.
 * @return array Menu args.
 */
function generate_page_menu_args($args)
{
}
/**
 * Remove our title if set.
 *
 * @since 1.3.18
 *
 * @param bool $title Whether the title is displayed or not.
 * @return bool Whether to display the content title.
 */
function generate_disable_title($title)
{
}
/**
 * Add resource hints to our Google fonts call.
 *
 * @since 1.3.42
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function generate_resource_hints($urls, $relation_type)
{
}
/**
 * Remove WordPress's default padding on images with captions
 *
 * @param int $width Default WP .wp-caption width (image width + 10px).
 * @return int Updated width to remove 10px padding.
 */
function generate_remove_caption_padding($width)
{
}
/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages.
 *
 * @param string $url The input URL.
 * @param int    $id The ID of the post.
 */
function generate_enhanced_image_navigation($url, $id)
{
}
/**
 * Determine whether blog/site has more than one category.
 *
 * @since 1.2.5
 *
 * @return bool True of there is more than one category, false otherwise.
 */
function generate_categorized_blog()
{
}
/**
 * Flush out the transients used in {@see generate_categorized_blog()}.
 *
 * @since 1.2.5
 */
function generate_category_transient_flusher()
{
}
/**
 * Set up our colors for the color picker palettes and filter them so you can change them.
 *
 * @since 1.3.42
 */
function generate_get_default_color_palettes()
{
}
/**
 * Check to see if we should include the full Font Awesome library or not.
 *
 * @since 2.0
 *
 * @param bool $essentials The existing value.
 * @return bool
 */
function generate_set_font_awesome_essentials($essentials)
{
}
/**
 * Skips caching of the dynamic CSS if set to false.
 *
 * @since 2.0
 *
 * @param bool $cache The existing value.
 * @return bool
 */
function generate_skip_dynamic_css_cache($cache)
{
}
/**
 * Set any necessary headers.
 *
 * @param array $headers The existing headers.
 *
 * @since 2.3
 */
function generate_set_wp_headers($headers)
{
}
/**
 * Adds microdata to elements.
 *
 * @since 3.0.0
 * @param string $output The existing output after the class attribute.
 * @param string $context What element we're targeting.
 */
function generate_set_microdata_markup($output, $context)
{
}
/**
 * Enqueue scripts in the footer.
 *
 * @since 3.1.0
 */
function generate_do_a11y_scripts()
{
}
/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_body_classes($classes)
{
}
/**
 * Adds custom classes to the header.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_top_bar_classes($classes)
{
}
/**
 * Adds custom classes to the right sidebar.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_right_sidebar_classes($classes)
{
}
/**
 * Adds custom classes to the left sidebar.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_left_sidebar_classes($classes)
{
}
/**
 * Adds custom classes to the content container.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_content_classes($classes)
{
}
/**
 * Adds custom classes to the header.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_header_classes($classes)
{
}
/**
 * Adds custom classes to inside the header.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_inside_header_classes($classes)
{
}
/**
 * Adds custom classes to the navigation.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_navigation_classes($classes)
{
}
/**
 * Adds custom classes to the inner navigation.
 *
 * @param array $classes The existing classes.
 * @since 1.3.41
 */
function generate_inside_navigation_classes($classes)
{
}
/**
 * Adds custom classes to the menu.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_menu_classes($classes)
{
}
/**
 * Adds custom classes to the footer.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_footer_classes($classes)
{
}
/**
 * Adds custom classes to the footer.
 *
 * @param array $classes The existing classes.
 * @since 0.1
 */
function generate_inside_footer_classes($classes)
{
}
/**
 * Adds custom classes to the <main> element
 *
 * @param array $classes The existing classes.
 * @since 1.1.0
 */
function generate_main_classes($classes)
{
}
/**
 * Adds custom classes to the #page element
 *
 * @param array $classes The existing classes.
 * @since 3.0.0
 */
function generate_do_page_container_classes($classes)
{
}
/**
 * Adds custom classes to the comment author element
 *
 * @param array $classes The existing classes.
 * @since 3.0.0
 */
function generate_do_comment_author_classes($classes)
{
}
/**
 * Adds custom classes to the <article> element.
 * Remove .hentry class from pages to comply with structural data guidelines.
 *
 * @param array $classes The existing classes.
 * @since 1.3.39
 */
function generate_post_classes($classes)
{
}
/**
 * Adds any scripts for this meta box.
 *
 * @since 2.0
 *
 * @param string $hook The current admin page.
 */
function generate_enqueue_meta_box_scripts($hook)
{
}
/**
 * Generate the layout metabox
 *
 * @since 2.0
 */
function generate_register_layout_meta_box()
{
}
/**
 * Build our meta box.
 *
 * @since 2.0
 *
 * @param object $post All post information.
 */
function generate_do_layout_meta_box($post)
{
}
/**
 * Saves the sidebar layout meta data.
 *
 * @since 2.0
 * @param int $post_id Post ID.
 */
function generate_save_layout_meta_data($post_id)
{
}
/**
 * Set up WooCommerce
 *
 * @since 1.3.47
 */
function generate_setup_woocommerce()
{
}
/**
 * Get the tag name for our WooCommerce wrappers.
 *
 * @since 3.2.0
 */
function generate_get_woocommerce_wrapper_tagname()
{
}
/**
 * Add WooCommerce starting wrappers
 *
 * @since 1.3.22
 */
function generate_woocommerce_start()
{
}
/**
 * Add WooCommerce ending wrappers
 *
 * @since 1.3.22
 */
function generate_woocommerce_end()
{
}
/**
 * Add WooCommerce CSS
 *
 * @since 1.3.45
 */
function generate_woocommerce_css()
{
}
/**
 * Add bbPress CSS
 *
 * @since 1.3.45
 */
function generate_bbpress_css()
{
}
/**
 * Add BuddyPress CSS
 *
 * @since 1.3.45
 */
function generate_buddypress_css()
{
}
/**
 * Add Beaver Builder CSS
 *
 * Beaver Builder pages set to no sidebar used to automatically be full width, however
 * now that we have the Page Builder Container meta box, we want to give the user
 * the option to set the page to full width or contained.
 *
 * We can't remove this CSS as people who are depending on it will lose their full
 * width layout when they update.
 *
 * So instead, we only apply this CSS to posts older than the date of this update.
 *
 * @since 1.3.45
 */
function generate_beaver_builder_css()
{
}
/**
 * Add CSS for third-party plugins.
 *
 * @since 3.0.1
 */
function generate_do_third_party_plugin_css()
{
}
/**
 * Add CSS to ensure compatibility with GP Premium.
 *
 * @since 3.0.0
 */
function generate_do_pro_compatibility()
{
}
/**
 * Set the menu item arrow directions for Secondary and Slideout navs.
 *
 * @since 3.0.0
 * @param string $arrow_direction The current direction.
 * @param object $args The args for the current menu.
 * @param int    $depth The current depth of the menu item.
 */
function generate_set_pro_menu_item_arrow_directions($arrow_direction, $args, $depth)
{
}
/**
 * Set defaults in our pro Menu Plus module.
 *
 * @since 3.0.0
 * @param array $defaults The existing defaults.
 */
function generate_set_menu_plus_compat_defaults($defaults)
{
}
/**
 * Set defaults in our pro Spacing module.
 *
 * @since 3.0.0
 * @param array $defaults The existing defaults.
 */
function generate_set_spacing_compat_defaults($defaults)
{
}
/**
 * Add CSS to our premium Page Heroes.
 *
 * @since 3.0.0
 * @param string $css_output Existing CSS.
 * @param array  $options The Header Element options.
 */
function generate_do_pro_page_hero_css($css_output, $options)
{
}
/**
 * Alter some Customizer options in the pro version.
 *
 * @since 3.0.0
 * @param object $wp_customize The Customizer object.
 */
function generate_pro_compat_customize_register($wp_customize)
{
}
/**
 * Do basic compatibility with GP Premium versions.
 *
 * @since 3.0.0
 */
function generate_do_pro_compatibility_setup()
{
}
/**
 * Tell GP about our active pro menus.
 *
 * @since 3.1.0
 * @param boolean $has_active_menu Whether we have an active menu.
 */
function generate_do_pro_active_menus($has_active_menu)
{
}
/**
 * Make changes to the Customizer in the Pro version.
 */
function generate_do_customizer_compatibility_setup()
{
}
/**
 * Build the archive title
 *
 * @since 1.3.24
 */
function generate_archive_title()
{
}
/**
 * Alter the_archive_title() function to match our original archive title function
 *
 * @since 1.3.45
 *
 * @param string $title The archive title.
 * @return string The altered archive title
 */
function generate_filter_the_archive_title($title)
{
}
/**
 * Output the archive description.
 *
 * @since 2.3
 */
function generate_do_archive_description()
{
}
/**
 * Add the search results title to the search results page.
 *
 * @since 3.1.0
 * @param string $template The template we're targeting.
 */
function generate_do_search_results_title($template)
{
}
/**
 * Template for comments and pingbacks.
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @param object $comment The comment object.
 * @param array  $args The existing args.
 * @param int    $depth The thread depth.
 */
function generate_comment($comment, $args, $depth)
{
}
/**
 * Add our comment reply link after the comment text.
 *
 * @since 2.4
 * @param object $comment The comment object.
 * @param array  $args The existing args.
 * @param int    $depth The thread depth.
 */
function generate_do_comment_reply_link($comment, $args, $depth)
{
}
/**
 * Set the default settings for our comments.
 *
 * @since 2.3
 *
 * @param array $defaults The existing defaults.
 * @return array
 */
function generate_set_comment_form_defaults($defaults)
{
}
/**
 * Customizes the existing comment fields.
 *
 * @since 2.1.2
 * @param array $fields The existing fields.
 * @return array
 */
function generate_filter_comment_fields($fields)
{
}
/**
 * Add the comments template to pages and single posts.
 *
 * @since 3.0.0
 * @param string $template The template we're targeting.
 */
function generate_do_comments_template($template)
{
}
/**
 * Prints the Post Image to post excerpts
 */
function generate_post_image()
{
}
/**
 * Build the page header.
 *
 * @since 1.0.7
 *
 * @param string $class The featured image container class.
 */
function generate_featured_page_header_area($class)
{
}
/**
 * Add page header above content.
 *
 * @since 1.0.2
 */
function generate_featured_page_header()
{
}
/**
 * Add post header inside content.
 * Only add to single post.
 *
 * @since 1.0.7
 */
function generate_featured_page_header_inside_single()
{
}
/**
 * Build our footer.
 *
 * @since 1.3.42
 */
function generate_construct_footer()
{
}
/**
 * Build our footer bar
 *
 * @since 1.3.42
 */
function generate_footer_bar()
{
}
/**
 * Add the copyright to the footer
 *
 * @since 0.1
 */
function generate_add_footer_info()
{
}
/**
 * Build our individual footer widgets.
 * Displays a sample widget if no widget is found in the area.
 *
 * @since 2.0
 *
 * @param int $widget_width The width class of our widget.
 * @param int $widget The ID of our widget.
 */
function generate_do_footer_widget($widget_width, $widget)
{
}
/**
 * Build our footer widgets.
 *
 * @since 1.3.42
 */
function generate_construct_footer_widgets()
{
}
/**
 * Build the back to top button
 *
 * @since 1.3.24
 */
function generate_back_to_top()
{
}
/**
 * Build the header.
 *
 * @since 1.3.42
 */
function generate_construct_header()
{
}
/**
 * Build the header contents.
 * Wrapping this into a function allows us to customize the order.
 *
 * @since 1.2.9.7
 */
function generate_header_items()
{
}
/**
 * Build the logo
 *
 * @since 1.3.28
 */
function generate_construct_logo()
{
}
/**
 * Build the site title and tagline.
 *
 * @since 1.3.28
 */
function generate_construct_site_title()
{
}
/**
 * Remove the logo from it's usual position.
 *
 * @since 2.3
 * @param array $order Order of the header items.
 */
function generate_reorder_inline_site_branding($order)
{
}
/**
 * Build the header widget.
 *
 * @since 1.3.28
 */
function generate_construct_header_widget()
{
}
/**
 * Add the site logo to our header.
 * Only added if we aren't using floats to preserve backwards compatibility.
 *
 * @since 3.0.0
 */
function generate_do_site_logo()
{
}
/**
 * Add the site branding to our header.
 * Only added if we aren't using floats to preserve backwards compatibility.
 *
 * @since 3.0.0
 */
function generate_do_site_branding()
{
}
/**
 * Add the header widget to our header.
 * Only used when grid isn't using floats to preserve backwards compatibility.
 *
 * @since 3.0.0
 */
function generate_do_header_widget()
{
}
/**
 * Build our top bar.
 *
 * @since 1.3.45
 */
function generate_top_bar()
{
}
/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 *
 * @since 1.3.42
 */
function generate_pingback_header()
{
}
/**
 * Add viewport to wp_head.
 *
 * @since 1.1.0
 */
function generate_add_viewport()
{
}
/**
 * Add skip to content link before the header.
 *
 * @since 2.0
 */
function generate_do_skip_to_content_link()
{
}
/**
 * Build the navigation.
 *
 * @since 0.1
 */
function generate_navigation_position()
{
}
/**
 * Build the mobile menu toggle in the header.
 *
 * @since 3.0.0
 */
function generate_do_header_mobile_menu_toggle()
{
}
/**
 * Menu fallback.
 *
 * @since 1.1.4
 *
 * @param array $args Existing menu args.
 */
function generate_menu_fallback($args)
{
}
/**
 * Generate the navigation based on settings
 *
 * It would be better to have all of these inside one action, but these
 * are kept this way to maintain backward compatibility for people
 * un-hooking and moving the navigation/changing the priority.
 *
 * @since 0.1
 */
function generate_add_navigation_after_header()
{
}
/**
 * Generate the navigation based on settings
 *
 * It would be better to have all of these inside one action, but these
 * are kept this way to maintain backward compatibility for people
 * un-hooking and moving the navigation/changing the priority.
 *
 * @since 0.1
 */
function generate_add_navigation_before_header()
{
}
/**
 * Generate the navigation based on settings
 *
 * It would be better to have all of these inside one action, but these
 * are kept this way to maintain backward compatibility for people
 * un-hooking and moving the navigation/changing the priority.
 *
 * @since 0.1
 */
function generate_add_navigation_float_right()
{
}
/**
 * Generate the navigation based on settings
 *
 * It would be better to have all of these inside one action, but these
 * are kept this way to maintain backward compatibility for people
 * un-hooking and moving the navigation/changing the priority.
 *
 * @since 0.1
 */
function generate_add_navigation_before_right_sidebar()
{
}
/**
 * Generate the navigation based on settings
 *
 * It would be better to have all of these inside one action, but these
 * are kept this way to maintain backward compatibility for people
 * un-hooking and moving the navigation/changing the priority.
 *
 * @since 0.1
 */
function generate_add_navigation_before_left_sidebar()
{
}
/**
 * Add dropdown icon if menu item has children.
 *
 * @since 1.3.42
 *
 * @param string   $title The menu item title.
 * @param WP_Post  $item All of our menu item data.
 * @param stdClass $args All of our menu item args.
 * @param int      $depth Depth of menu item.
 * @return string The menu item.
 */
function generate_dropdown_icon_to_menu_link($title, $item, $args, $depth)
{
}
/**
 * Add attributes to the menu item link when using the Click - Menu Item option.
 *
 * @since 3.5.0
 *
 * @param array    $atts The menu item attributes.
 * @param WP_Post  $item The current menu item.
 * @param stdClass $args The menu item args.
 * @param int      $depth The depth of the menu item.
 * @return array The menu item attributes.
 */
function generate_set_menu_item_link_attributes($atts, $item, $args, $depth)
{
}
/**
 * Add the search bar to the navigation.
 *
 * @since 1.1.4
 */
function generate_navigation_search()
{
}
/**
 * Add a container for menu bar items.
 *
 * @since 3.0.0
 */
function generate_do_menu_bar_item_container()
{
}
/**
 * Add menu bar items to the primary navigation.
 *
 * @since 3.0.0
 */
function generate_add_menu_bar_items()
{
}
/**
 * Add the navigation search button.
 *
 * @since 3.0.0
 */
function generate_do_navigation_search_button()
{
}
/**
 * Add search icon to primary menu if set.
 * Only used if using old float system.
 *
 * @since 1.2.9.7
 *
 * @param string   $nav The HTML list content for the menu items.
 * @param stdClass $args An object containing wp_nav_menu() arguments.
 * @return string The search icon menu item.
 */
function generate_menu_search_icon($nav, $args)
{
}
/**
 * Add search icon to mobile menu bar.
 * Only used if using old float system.
 *
 * @since 1.3.12
 */
function generate_mobile_menu_search_icon()
{
}
/**
 * Clone our sidebar navigation and place it below the header.
 * This places our mobile menu in a more user-friendly location.
 *
 * We're not using wp_add_inline_script() as this needs to happens
 * before menu.js is enqueued.
 *
 * @since 2.0
 */
function generate_clone_sidebar_navigation()
{
}
/**
 * Display navigation to next/previous pages when applicable.
 *
 * @since 0.1
 *
 * @param string $nav_id The id of our navigation.
 */
function generate_content_nav($nav_id)
{
}
/**
 * Remove the container and screen reader text from the_posts_pagination()
 * We add this in ourselves in generate_content_nav()
 *
 * @since 1.3.45
 *
 * @param string $template The default template.
 * @param string $class The class passed by the calling function.
 * @return string The HTML for the post navigation.
 */
function generate_modify_posts_pagination_template($template, $class)
{
}
/**
 * Output requested post meta.
 *
 * @since 2.3
 *
 * @param string $item The post meta item we're requesting.
 */
function generate_do_post_meta_item($item)
{
}
/**
 * Add svg icons or text to our post meta output.
 *
 * @since 2.4
 * @param string $output The existing output.
 * @param string $item The item to target.
 */
function generate_do_post_meta_prefix($output, $item)
{
}
/**
 * Remove post meta items from display if their individual filters are set.
 *
 * @since 3.0.0
 * @param array $items The post meta items.
 */
function generate_disable_post_meta_items($items)
{
}
/**
 * Get the post meta items in the header entry meta.
 *
 * @since 3.0.0
 */
function generate_get_header_entry_meta_items()
{
}
/**
 * Get the post meta items in the footer entry meta.
 *
 * @since 3.0.0
 */
function generate_get_footer_entry_meta_items()
{
}
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since 0.1
 */
function generate_posted_on()
{
}
/**
 * Prints HTML with meta information for the categories, tags.
 *
 * @since 1.2.5
 */
function generate_entry_meta()
{
}
/**
 * Prints the read more HTML to post excerpts.
 *
 * @since 0.1
 *
 * @param string $more The string shown within the more link.
 * @return string The HTML for the more link.
 */
function generate_excerpt_more($more)
{
}
/**
 * Prints the read more HTML to post content using the more tag.
 *
 * @since 0.1
 *
 * @param string $more The string shown within the more link.
 * @return string The HTML for the more link
 */
function generate_content_more($more)
{
}
/**
 * Add our post meta items to the page.
 *
 * @since 3.0.0
 */
function generate_add_post_meta()
{
}
/**
 * Build the post meta.
 *
 * @since 1.3.29
 */
function generate_post_meta()
{
}
/**
 * Build the footer post meta.
 *
 * @since 1.3.30
 */
function generate_footer_meta()
{
}
/**
 * Add our post navigation after post loops.
 *
 * @since 3.0.0
 * @param string $template The template of the current action.
 */
function generate_do_post_navigation($template)
{
}
/**
 * Returns the read more text for our posts.
 *
 * @since 3.4.0
 */
function generate_get_read_more_text()
{
}
/**
 * Returns the read more `aria-label` for our posts.
 *
 * @since 3.4.0
 */
function generate_get_read_more_aria_label()
{
}
/**
 * Create the search modal HTML.
 */
function generate_do_search_modal()
{
}
/**
 * Create the search modal trigger.
 */
function generate_do_search_modal_trigger()
{
}
/**
 * Enable the search modal.
 */
function generate_enable_search_modal()
{
}
/**
 * Do the modal CSS.
 *
 * @param Object $css The existing CSS object.
 */
function generate_do_search_modal_css($css)
{
}
/**
 * Add our search fields to the modal.
 */
function generate_do_search_fields()
{
}
/**
 * Construct the sidebars.
 *
 * @since 0.1
 */
function generate_construct_sidebars()
{
}
/**
 * Show sidebar widgets if no widgets are added to the sidebar area.
 *
 * @since 2.2
 *
 * @param string $area Left or right sidebar.
 */
function generate_do_default_sidebar_widgets($area)
{
}
/**
 * A wrapper function to get our options.
 *
 * @since 2.2
 *
 * @param string $option The option name to look up.
 * @return string The option value.
 */
function generate_get_option($option)
{
}
/**
 * Get the layout for the current page.
 *
 * @since 0.1
 *
 * @return string The sidebar layout location.
 */
function generate_get_layout()
{
}
/**
 * Get the footer widgets for the current page
 *
 * @since 0.1
 *
 * @return int The number of footer widgets.
 */
function generate_get_footer_widgets()
{
}
/**
 * Figure out if we should show the blog excerpts or full posts
 *
 * @since 1.3.15
 */
function generate_show_excerpt()
{
}
/**
 * Check to see if we should show our page/post title or not.
 *
 * @since 1.3.18
 *
 * @return bool Whether to show the content title.
 */
function generate_show_title()
{
}
/**
 * Check whether we should display the entry header or not.
 *
 * @since 3.0.0
 */
function generate_show_entry_header()
{
}
/**
 * Generate a URL to our premium add-ons.
 * Allows the use of a referral ID and campaign.
 *
 * @since 1.3.42
 *
 * @param string $url URL to premium page.
 * @param bool   $trailing_slash Whether we want to include a trailing slash.
 * @return string The URL to generatepress.com.
 */
function generate_get_premium_url($url = 'https://generatepress.com/premium', $trailing_slash = \true)
{
}
/**
 * Shorten our padding/margin values into shorthand form.
 *
 * @since 0.1
 *
 * @param int $top Top spacing.
 * @param int $right Right spacing.
 * @param int $bottom Bottom spacing.
 * @param int $left Left spacing.
 * @return string Element spacing values.
 */
function generate_padding_css($top, $right, $bottom, $left)
{
}
/**
 * Return the post URL.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since 1.2.5
 *
 * @see get_url_in_content()
 * @return string The Link format URL.
 */
function generate_get_link_url()
{
}
/**
 * Get the location of the navigation and filter it.
 *
 * @since 1.3.41
 *
 * @return string The primary menu location.
 */
function generate_get_navigation_location()
{
}
/**
 * Check if the logo and site branding are active.
 *
 * @since 2.3
 */
function generate_has_logo_site_branding()
{
}
/**
 * Create SVG icons.
 *
 * @since 2.3
 *
 * @param string $icon The icon to get.
 * @param bool   $replace Whether we're replacing an icon on action (click).
 */
function generate_get_svg_icon($icon, $replace = \false)
{
}
/**
 * Out our icon HTML.
 *
 * @since 2.3
 *
 * @param string $icon The icon to print.
 * @param bool   $replace Whether to include the close icon to be shown using JS.
 */
function generate_do_svg_icon($icon, $replace = \false)
{
}
/**
 * Get our media queries.
 *
 * @since 2.4
 *
 * @param string $name Name of the media query.
 * @return string The full media query.
 */
function generate_get_media_query($name)
{
}
/**
 * Display HTML classes for an element.
 *
 * @since 2.2
 *
 * @param string       $context The element we're targeting.
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_do_element_classes($context, $class = '')
{
}
/**
 * Retrieve HTML classes for an element.
 *
 * @since 2.2
 *
 * @param string       $context The element we're targeting.
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_element_classes($context, $class = '')
{
}
/**
 * Get the kind of schema we're using.
 *
 * @since 3.0.0
 */
function generate_get_schema_type()
{
}
/**
 * Get any necessary microdata.
 *
 * @since 2.2
 *
 * @param string $context The element to target.
 * @return string Our final attribute to add to the element.
 */
function generate_get_microdata($context)
{
}
/**
 * Output our microdata for an element.
 *
 * @since 2.2
 *
 * @param string $context The element to target.
 */
function generate_do_microdata($context)
{
}
/**
 * Whether to print hAtom output or not.
 *
 * @since 3.0.0
 */
function generate_is_using_hatom()
{
}
/**
 * Check whether we're using the Flexbox structure.
 *
 * @since 3.0.0
 */
function generate_is_using_flexbox()
{
}
/**
 * Check if we have any menu bar items.
 *
 * @since 3.0.0
 */
function generate_has_menu_bar_items()
{
}
/**
 * Check if we should include the default template part.
 *
 * @since 3.0.0
 * @param string $template The template to get.
 */
function generate_do_template_part($template)
{
}
/**
 * Check if we should use inline mobile navigation.
 *
 * @since 3.0.0
 */
function generate_has_inline_mobile_toggle()
{
}
/**
 * Build our the_title() parameters.
 *
 * @since 3.0.0
 */
function generate_get_the_title_parameters()
{
}
/**
 * Check whether we should display the default loop or not.
 *
 * @since 3.0.0
 */
function generate_has_default_loop()
{
}
/**
 * Detemine whether to output site branding container.
 *
 * @since 3.0.0
 */
function generate_needs_site_branding_container()
{
}
/**
 * Merge array of attributes with defaults, and apply contextual filter on array.
 *
 * The contextual filter is of the form `generate_attr_{context}`.
 *
 * @since 3.1.0
 *
 * @param string $context    The context, to build filter name.
 * @param array  $attributes Optional. Extra attributes to merge with defaults.
 * @param array  $settings   Optional. Custom data to pass to filter.
 * @return array Merged and filtered attributes.
 */
function generate_parse_attr($context, $attributes = array(), $settings = array())
{
}
/**
 * Build list of attributes into a string and apply contextual filter on string.
 *
 * The contextual filter is of the form `generate_attr_{context}_output`.
 *
 * @since 3.1.0
 *
 * @param string $context    The context, to build filter name.
 * @param array  $attributes Optional. Extra attributes to merge with defaults.
 * @param array  $settings   Optional. Custom data to pass to filter.
 * @return string String of HTML attributes and values.
 */
function generate_get_attr($context, $attributes = array(), $settings = array())
{
}
/**
 * Output our string of HTML attributes.
 *
 * @since 3.1.0
 *
 * @param string $context    The context, to build filter name.
 * @param array  $attributes Optional. Extra attributes to merge with defaults.
 * @param array  $settings   Optional. Custom data to pass to filter.
 */
function generate_do_attr($context, $attributes = array(), $settings = array())
{
}
/**
 * Build our editor color palette based on our global colors.
 *
 * @since 3.1.0
 */
function generate_get_editor_color_palette()
{
}
/**
 * Get our global colors.
 *
 * @since 3.1.0
 */
function generate_get_global_colors()
{
}
/**
 * Get our system default font.
 *
 * @since 3.1.0
 */
function generate_get_system_default_font()
{
}
/**
 * Check to see if we have a GP menu active.
 * This is primarily used to know whether we need to enqueue menu.js or not.
 *
 * @since 3.1.0
 */
function generate_has_active_menu()
{
}
/**
 * Check to see if we're using dynamic typography.
 *
 * @since 3.1.0
 */
function generate_is_using_dynamic_typography()
{
}
/**
 * Add inline script.
 *
 * @param string $handle The script handle to attach the inline script to.
 * @param array  $data   The data to be passed to the script.
 * @param string $var    The JavaScript variable name to assign the data to.
 * @param string $position The position to add the inline script.
 */
function generate_add_inline_script($handle, $data, $var, $position = 'before')
{
}
/**
 * Add Google Fonts to wp_head if needed.
 *
 * @since 0.1
 */
function generate_enqueue_google_fonts()
{
}
/**
 * Build our Typography options
 *
 * @since 0.1
 *
 * @param std_Class $wp_customize The Customize class.
 */
function generate_default_fonts_customize_register($wp_customize)
{
}
/**
 * Return an array of all of our Google Fonts.
 *
 * @since 1.3.0
 * @param string $amount How many fonts to return.
 * @return array The list of Google Fonts.
 */
function generate_get_all_google_fonts($amount = 'all')
{
}
/**
 * Return an array of all of our Google Fonts.
 *
 * @since 1.3.0
 */
function generate_get_all_google_fonts_ajax()
{
}
/**
 * Wrapper function to find variants for chosen Google Fonts
 * Example: generate_get_google_font_variation( 'Open Sans' )
 *
 * @since 1.3.0
 *
 * @param string $font The font to look up.
 * @param string $key The option to look up.
 */
function generate_get_google_font_variants($font, $key = '')
{
}
/**
 * Wrapper function to find the category for chosen Google Font
 * Example: generate_get_google_font_category( 'Open Sans' )
 *
 * @since 1.3.0
 *
 * @param string $font The name of our font.
 * @param string $key The ID of the font setting.
 * @return string The category of our font.
 */
function generate_get_google_font_category($font, $key = '')
{
}
/**
 * Wrapper function to create font-family value for CSS.
 *
 * @since 1.3.0
 *
 * @param string $font The name of our font.
 * @param string $settings The ID of the settings we're looking up.
 * @param array  $default The defaults for our $settings.
 * @return string The CSS value for our font family.
 */
function generate_get_font_family_css($font, $settings, $default)
{
}
/**
 * This function makes sure your selected typography option exists in the Customizer list
 * Why wouldn't it? Originally, all 800+ fonts were in each list. This has been reduced to 200.
 * This functions makes sure that if you were using a font that is now not included in the 200, you won't lose it.
 *
 * @since 1.3.40
 *
 * @param array $fonts The existing fonts.
 */
function generate_add_to_font_customizer_list($fonts)
{
}
/**
 * This function will check to see if your category and variants are saved
 * If not, it will set them for you
 * Generally, set_theme_mod isn't best practice, but this is here for migration purposes for a set amount of time only
 * Any time a user saves a font in the Customizer from now on, the category and variants are saved as theme_mods, so this function won't be necessary.
 *
 * @since 1.3.40
 */
function generate_typography_set_font_data()
{
}