<?php
    class pluginBruteForceProtection extends Plugin {
        
        public function init()
        {
            global $security;

            $this->dbFields = array(
                'minutesBlocked' => $security->db['minutesBlocked'],
                'numberFailuresAllowed' => $security->db['numberFailuresAllowed'],
                'savedAdminUriFilter' => 'admin' // tracks the last successfully written custom URL in the database
            );
        }

        // Method called on plugin settings on the admin area
        public function form()
        {
            global $L;
            global $security;
            global $users;
            $admin = new User("admin");

            $html = '<p class="alert alert-primary">' . $this->description() . '</p>';

            // Brute-force protection
            $html .= '<h3 class="mt-3">' . $L->get('brute-force-attack-title') . '</h3>';
            $html .= '<p>' . $L->get('brute-force-attack-description') . ' <a href="https://docs.bludit.com/en/security/brute-force-protection" lang="en">' . $L->get('bludit-documentation') . '</a>.</p>';

            // Brute-force protection settings
            $html .= '<h4 class="mt-4">' . $L->get('settings') . '</h4>';
            
                // Amount of minutes the IP is going to be blocked
                $html .= '<div>';
                $html .= '<label>' . $L->get('minutes-blocked') . '</label>';
                $html .= '<input name="minutesBlocked" type="number" class="form-control" min="1" value="' . $this->getValue('minutesBlocked') . '">';
                $html .= '</div>';

                // Number of failed attempts for the block to trigger
                $html .= '<div>';
                $html .= '<label>' . $L->get('number-failures-allowed') . '</label>';
                $html .= '<input name="numberFailuresAllowed" type="number" class="form-control" min="1" value="' . $this->getValue('numberFailuresAllowed') . '">';
                $html .= '</div>';

            // Suspicious IPs suspicious-ip
            $html .= '<h4 class="mt-4">' . $L->get('suspicious-ip') . '</h4>';

                $array = $security->db['blackList'];

                // Sort by lastFailure
                array_multisort(array_column($array, 'lastFailure'), SORT_DESC, $array);

                if ($array) {
                    $html .= '<table class="table table-striped">';

                        $html .= '<thead>';
                            $html .= '<tr>';
                                $html .= '<th scope="col">IP</th>';
                                $html .= '<th scope="col">' . $L->get('last-failure') . '</th>';
                                $html .= '<th scope="col">' . $L->get('number-of-failures') . '</th>';
                            $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody>';

                        foreach ($array as $ipKey => $ipValue) {
                            $html .= '<tr>';
                                $html .= '<th scope="row">' . $ipKey . '</th>';
                                foreach ($ipValue as $key => $value) {
                                    $html .= '<td>';
                                    $html .= $key === 'lastFailure' ?  date("Y-m-d h:i:s A", $value) : $value;
                                    $html .= '</td>';
                                }
                            $html .= '</tr>';   
                        }

                        $html .= '</tbody>';
                    $html .= '</table>';
                } else {
                    $html .= $L->get('no-ip-in-blacklist');
                }

            // Security recommendations
            $html .= '<h3 class="mt-3">' . $L->get('security-recommendations') . '</h3>';

                // Disable admin user 
                $html .= '<h4 class="mt-4">' . $L->get('disable-admin-user') . '</h4>';
                $html .= '<p>' . $L->get('the-admin-user-is');
                $html .= ' <b><span class="alert-';
                $html .= $admin->enabled() ? 'warning' : 'info';
                $html .= '">';
                $html .= $admin->enabled() ? $L->g('enabled') : $L->g('disabled');
                $html .= '</span></b>. ';
                $html .= $admin->enabled() ? $L->get('read') . ' <a href="https://docs.bludit.com/en/security/disable-admin-user" class="alert-link">' . $L->get('bludit-documentation') . '</a> ' . $L->get('for-disabling') . '.' : '';
                $html .= '</p>';

                // Customize admin URL

                // PRIORITY 1: Use the pending value if the user just clicked "Save"
                // PRIORITY 2: Use the value stored in the database (handles core updates seamlessly) -- Commented out
                // PRIORITY 3: Fallback to the current system constant                

                $pendingFilter = Session::get('pending_admin_uri_filter');
                // $dbFilter = $this->getValue('savedAdminUriFilter'); -- Commented out

                if (!empty($pendingFilter)) {
                $displayValue = $pendingFilter;
                /***** Commenting out Automatic recovery 
                } elseif (!empty($dbFilter)) {
                $displayValue = $dbFilter;
                *****/
                }                    
                else {
                $displayValue = ADMIN_URI_FILTER;
                }

                $html .= '<h4 class="mt-4">' . $L->get('customize-admin-url') . '</h4>';
                $html .= '<p>' . $L->get('the-admin-path-is');
                $html .= ' <b><code class="alert-';
                $html .= $displayValue === "admin" ? 'warning' : 'info';
                $html .= '">';
                $html .= $displayValue;
                $html .= '</code></b>. ';
                $html .= $displayValue === "admin" ? $L->get('read') . ' <a href="https://docs.bludit.com/en/security/custom-admin-panel-url" class="alert-link">' . $L->get('bludit-documentation') . '</a> ' . $L->get('for-changing') . '.' : '';
                $html .= '</p>';

                $file_path = PATH_BOOT . 'variables.php';
                
                // Check file write permissions and display a warning if necessary
                if (!is_writable($file_path)) {
                    $html .= '<p class="alert alert-warning"><code>' . $file_path . '</code>' .$L->get('file-not-writable-warning') . '</p>';
                } else {
                    $html .= '<div>';
                    $html .= '<label>' . $L->get('admin-uri-filter-constant') . '</label>';
                    // Input restricted to alphanumeric characters, hyphens, and underscores
                    $html .= '<input name="adminUriFilter" type="text" class="form-control" value="' . $displayValue . '" pattern="^[a-zA-Z0-9_-]+$" title="Alphanumeric characters, hyphens, and underscores only." required>';
                    $html .= '</div>';
                    $html .= '<p>' . $L->get('core-rewrite-info') . '</p>';
                }

            return $html;
        }

        // Method called when the user clicks on the Save button
        public function post()
        {
            // Get the new filter value from the form
            $newFilter = isset($_POST['adminUriFilter']) ? trim($_POST['adminUriFilter']) : '';

            // Validate that the value is strictly alphanumeric, hyphens, or underscores to prevent security issues or broken URLs
            if (!empty($newFilter) && preg_match('/^[a-zA-Z0-9_-]+$/', $newFilter)) {
                
                // Queue the rewrite if it's different from the CURRENT running constant
                if ($newFilter !== ADMIN_URI_FILTER) {
                    Session::set('pending_admin_uri_filter', $newFilter);
                }
                
                // Save the value into the plugin database
                $this->db['savedAdminUriFilter'] = $newFilter;
            }

            // Save the original brute force settings and the new database fields
            return parent::post();
        }

        // Hook executed after the admin area has loaded
        // Handles automatic recovery -- commented out -- and manual rewrite tasks smoothly after successful login
        public function afterAdminLoad()
        {
            global $L;

            // Check if the user is successfully logged into the admin area
            $login = new Login();
            if ($login->isLogged()) {

                /***** Commenting out Automatic recovery 
                
                // 1. AUTOMATIC RECOVERY: Triggered only AFTER successful login to avoid 404 deadlocks during login
                $savedFilter = $this->getValue('savedAdminUriFilter');
                
                // If the core was reset to 'admin' but the database has your custom URL saved
                if (ADMIN_URI_FILTER === 'admin' && !empty($savedFilter) && $savedFilter !== 'admin') {
                    $file_path = PATH_BOOT . 'variables.php';

                    if (is_writable($file_path)) {
                        $content = file_get_contents($file_path);
                        $pattern = "/define\s*\(\s*['\"]ADMIN_URI_FILTER['\"]\s*,\s*['\"].*?['\"]\s*\)\s*;/i";
                        $replacement = "define('ADMIN_URI_FILTER', '" . addslashes($savedFilter) . "');";
                        
                        $new_content = preg_replace($pattern, $replacement, $content);

                        if ($new_content !== null && file_put_contents($file_path, $new_content, LOCK_EX) !== false) {
                            // Display the success notification immediately on the dashboard
                            Alert::set(sprintf($L->get('core-reset-auto-recovered-alert'), $savedFilter));
                        }
                    }
                }

                *****/

                // 2. MANUAL SAVE REWRITE: Check if there is a pending URL filter rewrite queued in the session from a form save
                $pendingFilter = Session::get('pending_admin_uri_filter');

                if ($pendingFilter) {
                    $file_path = PATH_BOOT . 'variables.php';

                    if (is_writable($file_path)) {
                        $content = file_get_contents($file_path);
                        $pattern = "/define\s*\(\s*['\"]ADMIN_URI_FILTER['\"]\s*,\s*['\"].*?['\"]\s*\)\s*;/i";
                        $replacement = "define('ADMIN_URI_FILTER', '" . addslashes($pendingFilter) . "');";
                        
                        $new_content = preg_replace($pattern, $replacement, $content);

                        if ($new_content !== null && file_put_contents($file_path, $new_content, LOCK_EX) !== false) {
                            // Display a success alert message to the user before they get disconnected
                            $alertMessage = sprintf($L->get('core-rewrite-success-alert'), $pendingFilter);
                            Alert::set($alertMessage);
                        }
                    }
                    // Clear the queue so it only executes once
                    Session::remove('pending_admin_uri_filter');
                }
            }
        }

        public function beforeAdminLoad() {
            global $security;
            $security->db['minutesBlocked'] = $this->getValue('minutesBlocked');
            $security->db['numberFailuresAllowed'] = $this->getValue('numberFailuresAllowed');
        }
    }
?>