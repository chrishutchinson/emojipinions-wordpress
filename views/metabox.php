<table class="form-table form-table--emoji">
    <thead>
      <tr>
        <th></th>
        <th><?php _e( 'Emoji', $textDomain ); ?></th>
        <th><?php _e( 'Votes', $textDomain ); ?></th>
        <th><?php _e( 'Remove', $textDomain ); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      if( $hasEmoji ) {
        foreach($emoji as $key => $e) {
          ?>
          <tr id="emoji-<?php echo $key; ?>">
            <th scope="row">
              <label><?php _e( 'Emoji #', $textDomain ); ?></label>
            </th>
            <td>
              <input type="text" name="_emoji[]" value="<?php echo $e['emoji']; ?>" />
            </td>
            <td>
              <input type="number" min="0" name="_emoji_count[]" value="<?php echo $e['count']; ?>" />
            </td>
            <td class="emoji-remove-td">
              <a class="emoji-remove" href="#" data-action="removeEmoji">
                <span class="emoji-remove-icon">
                  <span class="screen-reader-text"><?php _e( 'Remove emoji', $textDomain ); ?></span>
                </span>
              </a>
            </td>
          </tr>
          <?php
          $emojiNumber++;
        }
      } else {
        ?>
        <tr>
          <th scope="row">
            <label><?php _e( 'Emoji #', $textDomain ); ?></label>
          </th>
          <td>
            <input type="text" name="_emoji[]" value="" />
          </td>
          <td>
            <input type="number" min="0" name="_emoji_count[]" value="1" />
          </td>
          <td>
            <a class="emoji-remove" href="#" data-action="removeEmoji">
              <span class="emoji-remove-icon">
                <span class="screen-reader-text"><?php _e( 'Remove emoji', $textDomain ); ?></span>
              </span>
            </a>
          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
</table>

<script id="emojipinions__adminHtml" type="text/x-handlebars-template">
  <tr>
    <th scope="row">
      <label><?php _e( 'Emoji #', $textDomain ); ?></label>
    </th>
    <td>
      <input type="text" name="_emoji[]" value="" />
    </td>
    <td>
      <input type="number" min="0" name="_emoji_count[]" value="1" />
    </td>
    <td>
      <a class="emoji-remove" href="#" data-action="removeEmoji">
        <span class="emoji-remove-icon">
          <span class="screen-reader-text"><?php _e( 'Remove emoji', $textDomain ); ?></span>
        </span>
      </a>
    </td>
  </tr>
</script>

<a role="button" href="#" class="button button-secondary" data-action="addEmoji" data-append=".form-table--emoji tbody">+ <?php _e( 'Add emoji', $textDomain ); ?></a>