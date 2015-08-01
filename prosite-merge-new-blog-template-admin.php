<?php 
		global $wpdb;

		if ( !is_super_admin() ) {
			echo "<p>" . __('Nice Try...', 'psts') . "</p>";  //If accessed properly, this message doesn't appear.
			return;
		}
		
		if($_POST['pmnbt_hidden'] == 'Y') {
			//Form data sent
			$table_name = $wpdb->prefix . 'pmnbt';
			$results = $wpdb->get_results("SELECT * from $table_name ORDER BY level ASC");
			//for($i = 0; $i<sizeof($_POST["$level_field"]); $i++){
				//$l = $_POST['level_field'][$i];
				//$t = $_POST['option_field'][$i];//strstr($_POST['option_field'], ' -', true);
				//if (!$results) {
				$i = 0;
				foreach ($results as $result){
					/*	$wpdb->query( $wpdb->prepare("INSERT INTO $table_name
						( level, template ) VALUES ( %d, %d )",
						$level_field[1], $option_field[1]) );
					}*/
					if ($result->template != $_POST['option_field'][$i]) {
						$wpdb->query( $wpdb->prepare("UPDATE $table_name
						SET template=%d WHERE level=%d", $_POST['option_field'][$i], $result->level));
					}
					$i = $i +1;
				}
			//}
			?>
			<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
			<?php
		} else {
			//Normal page display
			//Normal page display
			$table_name = $wpdb->prefix . 'pmnbt';
			$results = $wpdb->get_results("SELECT template from $table_name");
			if ($results) {
				$ii = 1;
				$templates_table = $wpdb->base_prefix . 'nbt_templates';
				foreach ($results as $row){
					$t[$ii] = $row->template;
					$r = $wpdb->get_row( $wpdb->prepare( "SELECT * from $templates_table WHERE id =%d", $t[$ii] ) );
					$role[$ii] = $r->name;
					$ii = $ii + 1;
				}
			}
			
		}
	
		?>
		<div class="wrap">
    <h2><?php _e('Prosite Merge New Blog Template', 'psts'); ?></h2>
    <?php

    $levels = (array)get_site_option('psts_levels');

		$level_list = get_site_option('psts_levels');
		$last_level = (is_array($level_list)) ? count($level_list) : 0;
		$table_name = $wpdb->prefix . 'pmnbt';
		foreach ($level_list as $level_code => $level){
			$level_table = $wpdb->get_row( $wpdb->prepare( "SELECT * from $table_name WHERE level=%d", $level_code ) );
			if ( count($level_table) == 0 ){
				$wpdb->query( $wpdb->prepare("INSERT INTO $table_name
						( level, template ) VALUES ( %d, %d )", $level_code, 1) );
			}
		}
		//$periods = (array)$this->get_setting('enabled_periods');
	
	$settings = nbt_get_settings();
            $templates = array();
            foreach ( $settings['templates'] as $key => $template ) {
                if ( ! is_main_site( absint( $template['blog_id'] ) ) )
                    $templates[$key] = $template['ID'];// . ' - ' .$template['name'];
            }
		?>

		<form id="form-level-list" action="" method="post">
		        <input type="hidden" name="pmnbt_hidden" value="Y">

    <?php wp_nonce_field('psts_levels') ?>

		<?php
		// define the columns to display, the syntax is 'internal name' => 'display name'
		$posts_columns = array(
			'level'        => __('Level', 'psts'),
			'name'     		 => __('Name', 'psts'),
			'template'      => __('Template', 'psts'),
			'role'      => __('Role', 'psts'),
			'option'      => __('Option', 'psts'),
		);
		?>
		<span class="description"><?php _e('When the user will select one prosite level, the following template will be automatically selected when new blog template will create the website.', 'psts') ?></span>
		<table width="100%" cellpadding="3" cellspacing="3" class="widefat">
			<thead>
				<tr>
					<th scope="col"><?php _e('Level', 'psts'); ?></th>
					<th scope="col"><?php _e('Name', 'psts'); ?></th>
					<th scope="col"><?php _e('Template', 'psts'); ?></th>
					<th scope="col"><?php _e('Role', 'psts'); ?></th>
					<th scope="col"><?php _e('Option', 'psts'); ?></th>
					<th scope="col"></th>
				</tr>
			</thead>
			<tbody id="the-list">
			<?php
			if ( is_array($level_list) && count($level_list) ) {
				$bgcolor = $class = '';
				foreach ($level_list as $level_code => $level) {
					$class = ('alternate' == $class) ? '' : 'alternate';

					echo '<tr class="'.$class.' blog-row">';
					
					foreach( $posts_columns as $column_name => $column_display_name ) {
						switch ( $column_name ) {
							case 'level': ?>
								<td scope="row" style="padding-left: 20px;" name="level_field[]">
									<strong><?php echo $level_code; ?></strong>
								</td>
							<?php
							break;

							case 'name': ?>
								<td scope="row">
         					<strong><?php echo esc_attr($level['name']) ?></strong>
								</td>
							<?php
							break;

							case 'template': ?>
								<td scope="row" name="t[]">
         					<strong><?php echo $t[$level_code]; ?></strong>
								</td>
							<?php
							break;

							case 'role': ?>
								<td scope="row" name="role[]">
         					<strong><?php echo $role[$level_code]; ?></strong>
								</td>
							<?php
							break;

							case 'option': ?>
<td scope="row">
<?php
$selector = '';
$selector .= esc_attr( $tag_name );
foreach ( $templates as $key => $value ) {
$label = esc_js( $value ); //( $esc_js ) ? esc_js( $value ) : stripslashes_deep( $value );
//$selector .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( selected( $settings['default'], esc_attr( $key ), false ) ) . '>' . $label . '</option>';
$selector .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( selected( $key == $settings['default'], true, false ) ) . '>' . $label . '</option>';
}
?>
<select name="option_field[]">
<?php
echo $selector;
?>
</select>
</td>
<?php
break;

						}
					}
					?>
					</tr>
					<?php
				}
			} else { ?>
				<tr style='background-color: <?php echo $bgcolor; ?>'>
					<td colspan="6"><?php _e('No levels yet.', 'psts') ?></td>
				</tr>
			<?php
			} // end if levels
			?>

			</tbody>
		</table>
		<p class="submit">
      <input type="submit" name="save_template" class="button-primary" value="<?php _e('Save', 'psts') ?>" />
    </p>

		</form>

		</div>
		