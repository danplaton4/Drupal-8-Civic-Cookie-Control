<?php

namespace Drupal\civiccookiecontrol\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\dblog\Plugin\views\wizard\Watchdog;
use Drupal\node\Entity\Node;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * The configuration form for cookie control settings.
 */
class CivicCookieControlSettings extends ConfigFormBase
{

    protected $countryManager;

    protected $itemsCount;

    protected $config;

    /**
     * Constructor.
     */
    public function __construct(CountryManager $countryManager)
    {
        $this->countryManager = $countryManager;
        $this->config = \Drupal::configFactory()
            ->getEditable('civiccookiecontrol.settings');

        _check_cookie_categories();

    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        // Instantiates this form class.
        return new static(
        // Load the service required to construct this class.
            $container->get('country_manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'civiccookiecontrol_config_form';
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $configData = $this->config->get();
        foreach ($configData as $key => $configValue) {
            if (strpos($key, 'civiccookiecontrol') !== FALSE) {
                if ($key == 'civiccookiecontrol_title_text' || $key == 'civiccookiecontrol_intro_text' || $key == 'civiccookiecontrol_full_text') {
                    if ($form_state->getValue($key) != '') {
                        $this->config->set($key, str_replace([
                            "\r\n",
                            "\n",
                            "\r",
                        ], '', $form_state->getValue($key)))->save();
                    }
                } else {
                    if ($key == 'civiccookiecontrol_api_key' || $key == 'civiccookiecontrol_product') {
                        if ($form_state->getValue($key) != $this->config->get($key)) {
                            \Drupal::service("router.builder")->rebuild();
                        }
                    }
                    if (array_key_exists($key, $form_state->getValues())) {
                        $this->config->set($key, $form_state->getValue($key))->save();
                    }
                }
            }
        }
        \Drupal::cache()->delete('civiccookiecontrol_config');
        parent::submitForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['civiccookiecontrol.settings'];
    }


    public static function validateApiKey($apiKey)
    {
        $domain = \Drupal::request()->getHost();

        $client = \Drupal::httpClient();
        $ccc_licenses_type = ['COMMUNITY' => 'CookieControl%20Free', 'PRO' => 'CookieControl%20Single-Site', 'PRO_MULTISITE' => 'CookieControl%20Multi-Site'];

        foreach ($ccc_licenses_type as $key => $licenseType) {
            $queryString = '?d=' . $domain . '&p=' . $licenseType . '&v=8&format=json&k=' . $apiKey;
            try {
                $request = $client->get("https://apikeys.civiccomputing.com/c/v" . $queryString);
                $respArray = Json::decode($request->getBody()->getContents());
                if ($respArray['valid'] == 1) {
                    return $key;
                }
            } catch (RequestException $ex) {
                \Drupal::logger('civiccookiecontrol')->notice($ex->getMessage());
            }
        }

        return false;
    }


    public function checkApiKey(array $form, FormStateInterface &$form_state)
    {
        if ($form_state->getTriggeringElement()['#name'] == 'civiccookiecontrol_api_key' && !empty($form['product_info']['civiccookiecontrol_api_key']['#value'])) {
            $response = new AjaxResponse();
            $key = $this->validateApiKey($form['product_info']['civiccookiecontrol_api_key']['#value']);
            if ($key !== false) {
                $response->addCommand(
                    new InvokeCommand("#edit-civiccookiecontrol-product-" . strtolower(str_replace("_", "-", $key)), 'loadLicense', [$key])
                );
                $msg = '<div><strong>' . t('Valid API Key. Click "save" to proceed.') . '</strong></div>';
                $response->addCommand(new HtmlCommand(".api-validation", $msg));
            } else {
                $errMsg = '<div class="form-item--error-message"><strong>' . t('Please provide a valid API key. For further information please contact Civic at queries@civicuk.com') . '</strong></div>';
                $response->addCommand(new HtmlCommand(".api-validation", $errMsg));
            }

            $form_state->setRebuild(TRUE);


            return $response;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $items_num = $form_state->get('items_count');
        global $base_url;

        $form['product_info'] = [
            '#type' => 'details',
            '#title' => t('Your Cookie Control Product Information'),
            '#open' => TRUE,
        ];

        $form['product_info']['civiccookiecontrol_api_key'] = [
            '#type' => 'textfield',
            '#title' => t('API Key Text'),
            '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                ->get('civiccookiecontrol_api_key'),
            '#required' => TRUE,
            '#suffix' => '<span class="api-validation"></span>',
            '#attributes' => [
                'class' => [
                    'container-inline',
                ],
            ],
            '#limit_validation_errors' => array(),
            '#ajax' => [
                'callback' => [$this, 'checkApiKey'], //'::checkApiKey',
                'event' => 'change',
                'wrapper' => 'api-validation',
                'effect' => 'fade',
                'method' => 'append',
                'progress' => [
                    'type' => 'throbber',
                    'message' => t('Validating Api key...'),
                ],
            ],
            '#description' => 'The API Key received for your deployment of Cookie Control. If in doubt, please contact the Civic helpdesk on helpdesk@civicuk.com, and please include the email and the domain you registered with in your email.',
        ];


        $form['product_info']['civiccookiecontrol_product'] = [
            '#type' => 'radios',
            '#title' => t('Product License Type'),
            '#options' => [
                'COMMUNITY' => t('Community Edition'),
                'PRO' => t('Pro Edition'),
                'PRO_MULTISITE' => t('Pro Multisite Edition'),
            ],
            '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                ->get('civiccookiecontrol_product'),
            '#required' => TRUE,
            '#description' => 'The type of obtained Cookie Control License that is tied to your API Key.',
        ];

        if ($this->validateApiKey(\Drupal::config('civiccookiecontrol.settings')->get('civiccookiecontrol_api_key')) == \Drupal::config('civiccookiecontrol.settings')->get('civiccookiecontrol_product')) {
            $form['product_info']['civiccookiecontrol_log_consent'] = [
                '#type' => 'radios',
                '#title' => t("Log user's granting or revoking of consent."),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_log_consent') ? 1 : 0,
                '#description' => t("Whether or not Cookie Control should record the user's granting or revoking of consent. Please note, this is also dependent on you haven agreed with CIVIC's data processor agreement. You need to sign in and accept the data processor agreement otherwise setting this option will have no effect."),
            ];

            $form['product_info']['civiccookiecontrol_encode_cookie'] = [
                '#type' => 'radios',
                '#title' => t("Encode Cookie."),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_encode_cookie') ? 1 : 0,
                '#description' => t("Determines whether or not the value of Cookie Control's own cookie should be encoded as a Uniform Resource Identifier (URI) component."),
            ];

            $form['product_info']['civiccookiecontrol_sub_domains'] = [
                '#type' => 'radios',
                '#title' => t("Make Cookie Control's own Cookie accessible to all subdomains."),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_sub_domains') ? 1 : 0,
                '#description' => t("Determines whether Cookie Control's own cookie is saved to the top level domain, and therefore accessible on all sub domains, or disabled and saved only to the request host."),
            ];

            $form['text_options'] = [
                '#type' => 'details',
                '#title' => t('Customising Appearance, Text and Behaviour'),
                '#open' => TRUE,
            ];

            $form['text_options']['civiccookiecontrol_warning']['#markup'] = "<div class=\"messages messages--warning\">Please note, we do not store information of any kind until the user opts into one of your cookie categories. If this never happens and initialState is set to open, the module will re-appear on each subsequent page load.</div>";

            $form['text_options']['civiccookiecontrol_initial_state'] = [
                '#type' => 'radios',
                '#title' => t('Pop up by default'),
                '#options' => [
                    'OPEN' => t('Open'),
                    'CLOSED' => t('Closed'),
                    'NOTIFY' => t('Notify'),
		    'TOP' => t('Top')
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_initial_state'),
                '#description' => t("Choose whether the Cookie Control user interface (UI) is open by default on page load. This makes it much more explicit that you're seeking user's consent for the use of cookies and may be a safer option in terms of compliance."),
            ];

            $form['text_options']['civiccookiecontrol_layout'] = [
                '#type' => 'radios',
                '#title' => t('Layout'),
                '#options' => [
                    'SLIDEOUT' => t('Slideout'),
                    'POPUP' => t('Popup'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_layout'),
                '#description' => t("Choose the layout of the Cookie Control user interface (UI)."),
            ];

            $form['text_options']['civiccookiecontrol_notify_once'] = [
                '#type' => 'radios',
                '#title' => t("Display cookie control's initial state only once."),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_notify_once') ? 1 : 0,
                '#description' => t("Determines whether the module only shows its initialState once, or if it continues to replay on subsequent page loads until the user has directly interacted with it - by either toggling on / off a category, accepting the recommended settings, or dismissing the module."),
            ];

            $form['text_options']['civiccookiecontrol_reject_button'] = [
                '#type' => 'radios',
                '#title' => t("Display reject button."),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_reject_button') ? 1 : 0,
                '#description' => t("Determines whether the module shows a reject button alongside the accept button on the notification bar, or alongside the 'accept recommended settings' button when the panel is open. Should the user click this, all optionalCookies will be revoked."),
            ];

            $form['text_options']['civiccookiecontrol_toggle_type'] = [
                '#type' => 'radios',
                '#title' => t('Toggle Type'),
                '#options' => [
                    'slider' => t('Slider'),
                    'checkbox' => t('Checkbox'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_toggle_type'),
                '#description' => t("Determines the control toggle for each item within optionalCookies. Possible values are either slider or checkbox."),
            ];

            $form['text_options']['civiccookiecontrol_close_style'] = [
                '#type' => 'radios',
                '#title' => t('Close Style'),
                '#options' => [
                    'icon' => t('Icon'),
                    'labelled' => t('Labelled'),
                    'button' => t('Button'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_close_style'),
                '#description' => t("Determines the closing behaviour of the module. Possible values are either icon, labelled or button."),
            ];

            $form['text_options']['civiccookiecontrol_close_label'] = [
                '#type' => 'textfield',
                '#title' => t('Close Label'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_close_label'),
            ];

            $form['text_options']['civiccookiecontrol_settings_style'] = [
                '#type' => 'radios',
                '#title' => t('Settings Style'),
                '#options' => [
                    'button' => t('Button'),
                    'link' => t('Link'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_settings_style'),
                '#description' => t("Determines the appearance of the settings button on the notification bar. Possible values are either button or link."),
            ];

            $form['text_options']['civiccookiecontrol_consent_cookie_expiry'] = [
                '#type' => 'textfield',
                '#title' => t('Consent cookie expiration(days)'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_consent_cookie_expiry'),
                '#description' => 'Controls how many days the consent of the user will be remembered for. Defaults to 90 days. This setting will apply globally to all categories.',
                '#required' => TRUE,
            ];

            $form['text_options']['civiccookiecontrol_widget_position'] = [
                '#type' => 'radios',
                '#title' => t('Widget Position'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_widget_position'),
                '#options' => [
                    'LEFT' => t('Left'),
                    'RIGHT' => t('Right'),
                ],
            ];

            $form['text_options']['civiccookiecontrol_widget_theme'] = [
                '#type' => 'radios',
                '#title' => t('Widget theme'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_widget_theme'),
                '#options' => [
                    'LIGHT' => t('Light'),
                    'DARK' => t('Dark'),
                ],
            ];

            $form['text_options']['civiccookiecontrol_title_text'] = [
                '#type' => 'textarea',
                '#title' => t('Title Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_title_text'),
                '#description' => t('On window-style widgets, this text appears as the first sentence of the window. On bar-style widgets, this is the only text visible on the bar before it is expanded.'),
            ];

            $form['text_options']['civiccookiecontrol_intro_text'] = [
                '#type' => 'textarea',
                '#title' => t('Introductory Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_intro_text'),
                '#description' => t('Some short, sharp copy to introduce the role of cookies on your site. On window-style widgets, this text is concatenated to the title and appears together. On bar-style widgets, this is the first text displayed in the widget when the information panel is visible.'),
            ];

            $form['text_options']['civiccookiecontrol_necessary_title_text'] = [
                '#type' => 'textarea',
                '#title' => t('Necessary Title Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_necessary_title_text'),
            ];

            $form['text_options']['civiccookiecontrol_necessary_desc_text'] = [
                '#type' => 'textarea',
                '#title' => t('Necessary Description Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_necessary_desc_text'),
            ];

            $form['text_options']['civiccookiecontrol_notify_title_text'] = [
                '#type' => 'textarea',
                '#title' => t('Notify Title Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_notify_title_text'),
            ];

            $form['text_options']['civiccookiecontrol_notify_desc_text'] = [
                '#type' => 'textarea',
                '#title' => t('Notify Description Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_notify_desc_text'),
            ];

            $form['text_options']['civiccookiecontrol_third_party_title_text'] = [
                '#type' => 'textarea',
                '#title' => t('Third Party Cookies Title Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_third_party_title_text'),
            ];

            $form['text_options']['civiccookiecontrol_third_party_desc_text'] = [
                '#type' => 'textarea',
                '#title' => t('Third Party Cookies Description Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_third_party_desc_text'),
            ];

            $form['text_options']['civiccookiecontrol_on_text'] = [
                '#type' => 'textfield',
                '#title' => t('On Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_on_text'),
            ];

            $form['text_options']['civiccookiecontrol_off_text'] = [
                '#type' => 'textfield',
                '#title' => t('Off Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_off_text'),
            ];

            $form['text_options']['civiccookiecontrol_accept_recommended'] = [
                '#type' => 'textfield',
                '#title' => t('Accept Recommended Settings Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_accept_recommended'),
            ];

            $form['text_options']['civiccookiecontrol_reject_settings'] = [
                '#type' => 'textfield',
                '#title' => t('Reject Settings Text'),
                '#placeholder' => 'Reject All',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_reject_settings'),
            ];

            $form['text_options']['civiccookiecontrol_accept_text'] = [
                '#type' => 'textfield',
                '#title' => t('Accept Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_accept_text'),
            ];

            $form['text_options']['civiccookiecontrol_reject_text'] = [
                '#type' => 'textfield',
                '#title' => t('Reject Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_reject_text'),
            ];

            $form['text_options']['civiccookiecontrol_setting_text'] = [
                '#type' => 'textfield',
                '#title' => t('Settings Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_setting_text'),
            ];

            $form['text_options']['civiccookiecontrol_accessibility_alert'] = [
                '#type' => 'textfield',
                '#title' => t('Accessibility Alert'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_accessibility_alert'),
            ];

            $form['text_options']['civiccookiecontrol_onload'] = [
                '#type' => 'textarea',
                '#title' => t('On Load'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_onload'),
                '#description' => t('Defines a function to be triggered after the module initiates the defined configuration.'),
            ];

            $form['statement'] = [
                '#type' => 'details',
                '#title' => t('Privacy Statement'),
                '#description' => t("In the following fields you may add the Privacy Statement for your website."),
                '#open' => TRUE,
            ];

            $form['statement']['civiccookiecontrol_stmt_descr'] = [
                '#type' => 'textarea',
                '#title' => t('Statement Description'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_stmt_descr'),
            ];

            $form['statement']['civiccookiecontrol_stmt_name'] = [
                '#type' => 'textfield',
                '#title' => t('Statement Name'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_stmt_name'),
            ];

            $form['statement']['civiccookiecontrol_stmt_date'] = [
                '#type' => 'date',
                '#title' => t('Statement Updated Date'),
                '#format' => 'd/m/Y',
                '#date_date_format' => 'd/m/Y',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_stmt_date'),
            ];


            $civiccookiecontrol_privacynode = \Drupal::config('civiccookiecontrol.settings')
                ->get('civiccookiecontrol_privacynode');
            $form['statement']['civiccookiecontrol_privacynode'] = [
                '#type' => 'number',
                '#size' => 5,
                '#maxlength' => 7,
                '#min' => 1,
                '#step' => 1,
                '#title' => t('Privacy Policy Link'),
                '#field_prefix' => $base_url . '/node/',
                '#default_value' => $civiccookiecontrol_privacynode,
                '#description' => ("Specify a node ID which represents your site's privacy policy. This will be appended at the end of your introductory text."),
                '#required' => FALSE,
            ];

            // Display a link to the current privacy policy if set.
            if ($civiccookiecontrol_privacynode > 0) {
                $privacyNodeUrl = Link::createFromRoute(t("View existing privacy policy page"), 'entity.node.canonical', ['node' => $civiccookiecontrol_privacynode]);
                $form['statement']['cookiecontrol_privacynode']['#field_suffix'] = $privacyNodeUrl->toString();
            }

            $form['statement']['civiccookiecontrol_stmt_url'] = [
                '#type' => 'hidden',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_stmt_url'),
            ];

            $form['statement']['civiccookiecontrol_stmt_date'] = [
                '#type' => 'date',
                '#title' => t('Statement Updated Date'),
                '#format' => 'd/m/Y',
                '#date_date_format' => 'd/m/Y',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_stmt_date'),
            ];

            $form['custom_widget'] = [
                '#type' => 'details',
                '#title' => t('Custom Branding'),
                '#description' => t("With PRO and PRO_MULTISITE licenses, you are able to set all aspects of the module's styling, and remove any back links to CIVIC."),
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
                '#open' => TRUE,
            ];

            $form['custom_widget']['civiccookiecontrol_warning']['#markup'] = "<div class=\"messages messages--warning\">Please note, in changing the branding object you take responsibility for the module's accessibility standard. Should you set the <strong>removeIcon</strong> option to <strong>true</strong>, it is your responsibility to create your own ever present button that invokes <strong>CookieControl.toggle()</strong> so that users may still have consistent access to granting and revoking their consent.</div>";

            $form['custom_widget']['civiccookiecontrol_font_family'] = [
                '#type' => 'textfield',
                '#title' => t('Font Family'),
                '#placeholder' => 'Arial',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_font_family'),
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_font_size_title'] = [
                '#type' => 'textfield',
                '#title' => t('Font Size Title'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_font_size_title'),
                '#placeholder' => '1.2',
                '#suffix' => 'em',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_font_size_headers'] = [
                '#type' => 'textfield',
                '#title' => t('Font Size Headers'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_font_size_headers'),
                '#placeholder' => '1',
                '#suffix' => 'em',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_font_size'] = [
                '#type' => 'textfield',
                '#title' => t('Font Size Headers'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_font_size'),
                '#placeholder' => '0.8',
                '#suffix' => 'em',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];


            $form['custom_widget']['civiccookiecontrol_font_color'] = [
                '#type' => 'textfield',
                '#title' => t('Font Color'),
                '#placeholder' => '#fff',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_font_color'),
                '#attributes' => ['class' => ['colorfield']],
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];


            /*    $form['custom_widget']['civiccookiecontrol_font_size_intro'] = [
                  '#type' => 'textfield',
                  '#title' => t('Font Size Intro'),
                  '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_font_size_intro'),
                  '#placeholder' => '1.2',
                  '#suffix' => 'em',
                  '#states' => [
                // Action to take.
                    'invisible' => [
                      ':input[name=civiccookiecontrol_product]' => [
                        'value' => 'COMMUNITY',
                      ],
                    ],
                  ],
                ];*/


            $form['custom_widget']['civiccookiecontrol_background_color'] = [
                '#type' => 'textfield',
                '#title' => t('Background Color'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_background_color'),
                '#placeholder' => '#00a0e0',
                '#attributes' => ['class' => ['colorfield']],
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_accept_text_color'] = [
                '#type' => 'textfield',
                '#title' => t('Accept Text Color'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_accept_text_color'),
                '#placeholder' => '#000',
                '#attributes' => ['class' => ['colorfield']],
                '#description' => 'The CSS color that you\'d like to use for the text within the module\'s primary \'accept\' buttons.',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_accept_background_color'] = [
                '#type' => 'textfield',
                '#title' => t('Accept Button Background Color'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_accept_background_color'),
                '#attributes' => ['class' => ['colorfield']],
                '#placeholder' => '#000',
                '#description' => 'The CSS background-color that you\'d like to use for the module\'s primary \'accept\' buttons.',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_toggle_text'] = [
                '#type' => 'textfield',
                '#title' => t('Toggle Text'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_toggle_text'),
                '#placeholder' => '#000',
                '#attributes' => ['class' => ['colorfield']],
                '#description' => 'The CSS color that you\'d like to apply to the toggle button\'s text.',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_toggle_color'] = [
                '#type' => 'textfield',
                '#title' => t('Toggle Color'),
                '#placeholder' => '#f0f0f0',
                '#attributes' => ['class' => ['colorfield']],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_toggle_color'),
                '#description' => 'The CSS background-color that you\'d like to use for the movable part of the toggle slider.',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_toggle_background'] = [
                '#type' => 'textfield',
                '#title' => t('Toggle Background'),
                '#placeholder' => '#fff',
                '#attributes' => ['class' => ['colorfield']],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_toggle_background'),
                '#description' => 'The CSS background-color that you\'d like to use for the toggle background.',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_alert_text'] = [
                '#type' => 'textfield',
                '#title' => t('Alert text colour'),
                '#placeholder' => '#fff',
                '#attributes' => ['class' => ['colorfield']],
                '#description' => 'The CSS color that you\'d like to use within the alert areas, such as to announce manual user actions for third party cookies.',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_alert_text'),
                '#states' => [
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_alert_background'] = [
                '#type' => 'textfield',
                '#title' => t('Alert background colour'),
                '#placeholder' => '#111125',
                '#attributes' => ['class' => ['colorfield']],
                '#description' => 'The CSS background-color that you\'d like to use to highlight the alert areas, such as to announce manual user actions for third party cookies.',
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_alert_background'),
                '#states' => [
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            global $base_url;
            $config = \Drupal::config('system.theme');

            $form['custom_widget']['civiccookiecontrol_button_icon'] = [
                '#type' => 'textfield',
                '#title' => t('Button Icon (url)'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_button_icon'),
                '#placeholder' => $base_url . file_url_transform_relative(file_create_url(theme_get_setting('logo.url', $config->get('default')))),
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_button_icon_width'] = [
                '#type' => 'textfield',
                '#title' => t('Button Icon Width'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_button_icon_width'),
                '#placeholder' => 64,
                '#suffix' => 'px',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_button_icon_height'] = [
                '#type' => 'textfield',
                '#title' => t('Button Icon Height'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_button_icon_height'),
                '#placeholder' => 64,
                '#suffix' => 'px',
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_remove_icon'] = [
                '#type' => 'radios',
                '#title' => 'Remove Icon',
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_remove_icon') ? 1 : 0,
                '#description' => t("Choose if you want to remove the Cookie Control logo."),
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_remove_about_text'] = [
                '#type' => 'radios',
                '#title' => 'Remove About Text',
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_remove_about_text') ? 1 : 0,
                '#description' => t("Choose if you want to remove the Cookie Control text as part of the widget's header and the 'About this tool' section the widget's footer."),
                '#states' => [
                    // Action to take.
                    'invisible' => [
                        ':input[name=civiccookiecontrol_product]' => [
                            'value' => 'COMMUNITY',
                        ],
                    ],
                ],
            ];

            $form['custom_widget']['civiccookiecontrol_debug'] = [
                '#type' => 'radios',
                '#title' => t('Print Cookie Control configuration object in console.'),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_debug') ? 1 : 0,
                '#description' => t("Prints Cookie Control configuration object in console for debugging reasons."),
            ];

            $form['custom_widget']['civiccookiecontrol_drupal_admin'] = [
                '#type' => 'radios',
                '#title' => t('Display cookie control in Drupal backend.'),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_drupal_admin') ? 1 : 0,
                '#description' => t("Displays Cookie Control widget in the drupal backend."),
            ];


            $form['accessibility'] = [
                '#type' => 'details',
                '#title' => t('Accessibility'),
                '#description' => t("Determines the accessibility helpers available, such as the accesskey and keyboard focus style."),
                '#open' => TRUE,
            ];

            $form['accessibility']['civiccookiecontrol_access_key'] = [
                '#type' => 'textfield',
                '#title' => t('Access Key'),
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_access_key'),
                '#description' => t('Remaps the accesskey that the module is assigned to.')
            ];

            $form['accessibility']['civiccookiecontrol_highlight_focus'] = [
                '#type' => 'radios',
                '#title' => t('Use accentuated styling to highlight elements in focus.'),
                '#options' => [
                    TRUE => t("Yes"),
                    FALSE => t('No'),
                ],
                '#default_value' => \Drupal::config('civiccookiecontrol.settings')
                    ->get('civiccookiecontrol_highlight_focus') ? 1 : 0,
                '#description' => t("Determines if the module should use more accentuated styling to highlight elements in focus, or use the browser's outline default. If enabled, this property uses CSS filters to invert the module's colours. This should hopefully mean that a higher visual contrast is achieved, even with a custom branding."),
            ];
        }


        $form['#attached'] = [
            'library' => [
                'civiccookiecontrol/civiccookiecontrol.admin',
                'civiccookiecontrol/civiccookiecontrol.admin_css',
                'civiccookiecontrol/civiccookiecontrol.minicolors',
                'civiccookiecontrol/civiccookiecontrol.lct'
            ],
        ];
        // $form['#theme'] = 'cookiecontrol-admin-form';.
        $form_state->setCached(FALSE);

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Save Cookie Control Configuration'),
            '#button_type' => 'primary',
        ];

        return $form;

    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (empty($form_state->getValue('civiccookiecontrol_api_key'))) {
            $form_state->setValue('civiccookiecontrol_api_key', $form['product_info']['civiccookiecontrol_api_key']['#value']);
        }
        if ($form_state->getTriggeringElement()['#name'] != 'civiccookiecontrol_api_key') {
            // Check if the API key is provided.
            if ($form_state->getValue(['civiccookiecontrol_api_key']) == '' || $this->validateApiKey($form_state->getValue(['civiccookiecontrol_api_key'])) == false) {
                $form_state->setErrorByName('civiccookiecontrol_api_key', t('Please provide a valid API key. For further information please contact Civic at queries@civicuk.com'));
            }
            // Attempt to load in a specified privacy policy node id.
            if ($form_state->getValue(['civiccookiecontrol_privacynode']) > 0) {
                $node = Node::load($form_state->getValue('civiccookiecontrol_privacynode'));
                // If no node can be loaded give the user a suitable message prompt.
                if (!$node) {
                    $form_state->setErrorByName('civiccookiecontrol_privacynode', t('The specified privacy policy node id does not exist. Leave blank if you have not yet created a policy page.'));
                } else {
                    $form_state->setValue('civiccookiecontrol_stmt_url', $node->toUrl());
                }
            }
        }
    }

}
