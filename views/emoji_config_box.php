<table class="form-table form-table--emoji">
    <tbody>
      <?php
      if($hasEmoji) {
        foreach($emoji as $key => $e) {
          ?>
          <tr id="emoji-<?php echo $key; ?>">
            <th scope="row">
              <label>Emoji #</label>
            </th>
            <td>
              <input type="text" name="_emoji[]" value="<?php echo $e; ?>" />
            </td>
            <td>
              <a class="emoji-remove" href="#" data-action="removeEmoji">
                <span class="emoji-remove-icon">
                  <span class="screen-reader-text">Remove emoji</span>
                </span>
              </a>
            </td>
          </tr>
          <?php
          $emojiNumber++;
        }
      }
      ?>

      <tr>
        <th scope="row">
          <label>Emoji #</label>
        </th>
        <td>
          <input type="text" name="_emoji[]" value="" />
        </td>
        <td>
          <a class="emoji-remove" href="#" data-action="removeEmoji">
            <span class="emoji-remove-icon">
              <span class="screen-reader-text">Remove emoji</span>
            </span>
          </a>
        </td>
      </tr>
    </tbody>
</table>

<a role="button" href="#" class="button button-secondary" data-action="addEmoji" data-append=".form-table--emoji tbody">+ Add Emoji</a>