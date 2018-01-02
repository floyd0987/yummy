<?php
class Yummy_meta_box
{
    protected $_meta_box;

    // create meta box based on given data
    public function __construct($meta_box)
    {
        $this->_meta_box = $meta_box;
        add_action('admin_menu', array(&$this, 'add'));

        add_action('save_post', array(&$this, 'save'));
    }

    /// Add meta box for multiple post types
    public function add()
    {
        foreach ($this->_meta_box['pages'] as $page) {
            add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
        }
    }

    // Callback function to show fields in meta box
    public function show()
    {
        global $post;

        // Use nonce for verification
        echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

        echo '<table class="form-table">';

        foreach ($this->_meta_box['fields'] as $field) {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);

            echo '<tr>',
                    '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                    '<td>';
                    switch ($field['type']) {
                      case 'text':
                      echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                      '<br />', $field['desc'];
                      break;


                      case 'date':
                      echo '<input type="date" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:20%" />',
                      '<br />', $field['desc'];
                      break;

                      case 'time':
                      echo '<input type="time" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:20%" />',
                      '<br />', $field['desc'];
                      break;

                      case 'number':
                      echo '<input type="number" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:20%" />',
                      '<br />', $field['desc'];
                      break;

                      case 'email':
                      echo '<input type="email" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                      '<br />', $field['desc'];
                      break;

                      case 'textarea':
                      echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
                      '<br />', $field['desc'];
                      break;
                      case 'select':
                      echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                      foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                      }
                      echo '</select>';
                      break;
                      case 'radio':
                      foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                      }
                      break;
                      case 'checkbox':
                      echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                      break;
                    }
                    echo     '<td>',
                    '</tr>';
                  }

        echo '</table>';
    }

    // Save data from meta box
    public function save($post_id)
    {
        // verify nonce
        if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        foreach ($this->_meta_box['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];

            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }
    }
}


foreach (YUMMY_BOOKING_POST_META as $yummy_booking_post_meta) {
    $input_type = $this->returnFieldType($yummy_booking_post_meta);

    $meta_boxes[] = array(
      'id' => $yummy_booking_post_meta,
      'title' => Yummy::returnCleanField($yummy_booking_post_meta),
      'pages' => array('yummy-booking'), // multiple post types
      'context' => 'normal',
      'priority' => 'high',
      'fields' => array(
          array(
              'name' => $yummy_booking_post_meta,
              'desc' => 'Enter something here',
              'id' => $yummy_booking_post_meta,
              'type' => $input_type,

          )
      )
  );
}


define( 'YUMMY_TABLES_POST_META', array('yummy_table_seats','yummy_table_room','yummy_table_stars' ) );

foreach (YUMMY_TABLES_POST_META as $yummy_tables_post_meta) {
    $input_type = $this->returnFieldType($yummy_tables_post_meta);

    $meta_boxes[] = array(
      'id' => $yummy_tables_post_meta,
      'title' => Yummy::returnCleanField($yummy_tables_post_meta),
      'pages' => array('yummy-tables'), // multiple post types
      'context' => 'normal',
      'priority' => 'high',
      'fields' => array(
          array(
              'name' => $yummy_tables_post_meta,
              'desc' => 'Enter something here',
              'id' => $yummy_tables_post_meta,
              'type' => $input_type,

          )
      )
  );
}







foreach ($meta_boxes as $meta_box) {
    $my_box = new Yummy_meta_box($meta_box);
}
