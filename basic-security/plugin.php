<?php
    class pluginBruteForceProtection extends Plugin {
        public function init()
        {
            global $security;

            $this->dbFields = array(
                'minutesBlocked' => $security->db['minutesBlocked'],
                'numberFailuresAllowed' => $security->db['numberFailuresAllowed']
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

            // Info
            $html .= '<h3 class="mt-3">' . $L->get('brute-force-attack-title') . '</h3>';
            $html .= '<p>' . $L->get('brute-force-attack-description') . ' <a href="https://docs.bludit.com/en/security/brute-force-protection" lang="en">' . $L->get('bludit-documentation') . '</a>.</p>';

            // Settings
            $html .= '<h4 class="mt-3">' . $L->get('settings') . '</h4>';
            
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
            $html .= '<h4 class="mt-3">' . $L->get('suspicious-ip') . '</h4>';

                $array = $security->db['blackList'];

                // Sort by lastFailure
                array_multisort(array_column($array, 'lastFailure'), SORT_DESC, $array);

                if ($array) {
                    $html .= '<table class="table table-striped">';

                        $html .= '<caption>' . $L->get('suspicious-ip') . '</caption>';
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

            // Security recommendations security-recommendations
            $html .= '<h3 class="mt-3">' . $L->get('security-recommendations') . '</h3>';

                // Disable admin user 
                $html .= '<h5 class="mt-3">' . $L->get('disable-admin-user') . '</h5>';
                $html .= '<p class="alert alert-';
                $html .= $admin->enabled() ? 'warning' : 'info';
                $html .= '">' . $L->get('the-admin-user-is') . ' <b>';
                $html .= $admin->enabled() ? $L->g('enabled') : $L->g('disabled');
                $html .= '</b>. ';
                $html .= $admin->enabled() ? $L->get('read') . ' <a href="https://docs.bludit.com/en/security/disable-admin-user" class="alert-link">' . $L->get('bludit-documentation') . '</a> ' . $L->get('for-disabling') . '.' : '';
                $html .= '</p>';

                // Customize admin URL
                $html .= '<h5 class="mt-3">' . $L->get('customize-admin-url') . '</h5>';
                $html .= '<p class="alert alert-';
                $html .= ADMIN_URI_FILTER === "admin" ? 'warning' : 'info';
                $html .= '">' . $L->get('the-admin-path-is') . ' <code>' . ADMIN_URI_FILTER . '</code>. ';
                $html .= ADMIN_URI_FILTER === "admin" ? $L->get('read') . ' <a href="https://docs.bludit.com/en/security/custom-admin-panel-url" class="alert-link">' . $L->get('bludit-documentation') . '</a> ' . $L->get('for-changing') . '.' : '';
                $html .= '</p>';

            return $html;
        }

        public function beforeAdminLoad() {
            global $security;
            $security->db['minutesBlocked'] = $this->getValue('minutesBlocked');
            $security->db['numberFailuresAllowed'] = $this->getValue('numberFailuresAllowed');
        }
    }
?>