<? View::show_header('Logchecker'); ?>
<div class="thin">
<h2 class="center">Logchecker for dBpoweramp, eac &amp; xld by robotnik</h2>
  <table class="forum_post vertical_margin">
    <tr class="colhead">
      <td colspan="2">Upload file</td>
    </tr>
    <tr>
      <td>
        <form action="" method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="takeupload" />
          <input type="file" name="log" size="40" />
          <input type="submit" value="Upload log" name="submit" />
        </form>
      </td>
    </tr>
    <tr class="colhead">
      <td colspan="2">Paste log</td>
    </tr>
    <tr>
      <td>
        <form action="" method="post">
          <input type="hidden" name="action" value="takeupload" />
          <textarea rows="5" cols="60" name="pastelog" wrap="soft"></textarea>
          <input type="submit" value="Upload log" name="submit" />
        </form>
      </td>
    </tr>
	</table>
	<br />
	<br />
</div>
<? View::show_footer(); ?>