<?php
/*
Plugin Name: Form data
Version: 1.0
Author: Yura
*/


Class FormData
{
    /**
     * FormData constructor.
     */
    public function __construct()
    {
        //add_action('init', array($this, 'custom_post_type'));
        add_shortcode('form_data', array($this, 'my_custom_form') );
        add_action('admin_menu', array($this, 'panel_form_data') );
    }

    public function activate()
    {
        $this->create_table();
//        $this->custom_post_type();
//        flush_rewrite_rules();
    }

    public function add_data_form()
    {
        global $wpdb;

        $table = $wpdb->prefix.'form_data';

        $post_data = [
            'name'  => $this->sql_inj($_POST['name']),

            'email' => $this->sql_inj($_POST['email']),

            'text'  => $this->sql_inj($_POST['text'])
        ];

        $wpdb->insert( $table, $post_data);

        header("Refresh: 0");
    }

    public function sql_inj($val)
    {
        return trim(strip_tags(htmlspecialchars($val)));
    }

    public function destroy_data_form()
    {
        $id = (int) $_GET['id_form-data'];

        global $wpdb;

        $table = $wpdb->prefix.'form_data';

        $wpdb->delete( $table, array( 'ID' => $id ), array( '%d' ));

        header("Location:?page=form-data.php");
    }

    public function deactivate()
    {

    }

//    public function uninstall()
//    {
//
//    }

    public function custom_post_type()
    {
        register_post_type('form_data', ['public' => true, 'label' => 'Form data']);
    }

    public function panel_form_data()
    {
        add_menu_page('Form data', 'Form data', 'manage_options', 'form-data.php', array($this, 'show_form_data_content'),'dashicons-menu', 100);
    }

    public function show_form_data_content()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "form_data";

        $form_data = $wpdb->get_results( "SELECT * FROM $table_name" );

        $content = '';

        $content .= '<table class="table">
                       <thead>
                         <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Text</th>
                          <th>Actions</th>
                         </tr>
                        </thead><tbody>';

        foreach ($form_data as $row) {
            $content .= '<tr>
                           <td>' .$row->name. '</td>
                           <td>' .$row->email. '</td>
                           <td>' .$row->text. '</td>
                           <td>
                             <a href="?del_form-data=destroy&id_form-data='.$row->id.'" style="cursor:pointer;" title="Видалити">delete</a>
                           </td>
                         </tr>';
        }
        $content .= '</tbody></table>';

        echo $content;

    }
    public function my_custom_form()
    {
        $form = '';
        $form .= '<form method="post">';
        $form .= ' <div>
                     <label for="name">Name</label>
                     <input type="text" name="name" required>
                     </div>
             
                     <div>
                     <label for="email">Email</label>
                     <input type="email" name="email" required>
                     </div>
             
                     <div>
                     <label for="text">Text</label>
                     <textarea name="text" required></textarea>
                   </div>';

         $form.='<input type="submit" name="add_form_data" value="Send"/></form>';

        return $form;
    }

    private function create_table()
    {
        global $wpdb;

        $prefix = $wpdb->prefix . 'form_data';

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE IF NOT EXISTS `" . $prefix . "` (
                  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) NOT NULL,
                  `email` varchar(100) NOT NULL,
                  `text` tinytext,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        dbDelta( $sql );
    }
}

if ( class_exists( 'FormData') ) {
    $formData = new FormData();
}

register_activation_hook(__FILE__, array($formData, 'activate'));

register_deactivation_hook(__FILE__, array($formData, 'deactivate'));

wp_enqueue_style('form_data_stylesheet', plugins_url( 'public/css/bootstrap.min.css', __FILE__ ) );
wp_enqueue_script( 'form_data_jquery', plugins_url( 'public/js/jquery-3.5.1.min.js', __FILE__ ) );
wp_enqueue_script( 'form_data_jquery_validate', plugins_url( 'public/js/jquery.validate.min.js', __FILE__ ) );
wp_enqueue_script( 'form_data_script', plugins_url( 'public/js/script.js', __FILE__ ) );

if ( isset( $_POST['add_form_data'] ) ) {
    $formData->add_data_form();
}

if (isset($_GET['del_form-data']) and $_GET['del_form-data'] == 'destroy') {
        $formData->destroy_data_form();
}


