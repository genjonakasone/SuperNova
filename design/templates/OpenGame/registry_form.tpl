<style type="text/css"><!--
 @import url(./design/css/login.css);
--></style>

<div id="log_skipper"></div>

<div id="log_main">
  <div id="log_title">{L_log_reg} - {L_sys_universe} "{C_game_name}"</div>
  <div id="log_description">{L_log_reg_text0} <a href="{C_url_rules}"><u><font color="red">{L_reg_with_rules}</font></u></a>. {L_log_reg_text1}</div>

  <form name="registerForm" method="POST" action="" onsubmit="changeAction('register');" >
    <input type="hidden" name="id_ref" value="{id_ref}">
    <table width="340" align="center">
      <tbody>
        <tr>
          <th width="179">{L_User_name}</th>
          <th width="161"><input name="username" type="text" size="20" maxlength="20" /></th>
        </tr>

        <tr>
          <th>{L_neededpass}:</th>
          <th><input name="password" type="password" size="20" maxlength="20" /></th>
        </tr>

        <tr>
          <th>{L_E-Mail}:</th>
          <th><input name="email" type="text" size="20" maxlength="40" /></th>
        </tr>

        <tr>
          <th>{L_MainPlanet}:</th>
          <th><input name="planet_name" type="text" size="20" maxlength="20" /></th>
        </tr>

        <tr>
          <th>{L_Sex}:</th>
  
          <th>
            <select name="sex">
              <option value="M" selected="selected">{L_Male}</option>
              <option value="F">{L_Female}</option>
            </select>
          </th>
        </tr>

        <!--
        <tr>
          <th>{L_Languese}:</th>
          <th>
            <select name="language">
              <option value="ru" selected="selected">{ru}</option>
            </select>
          </th>
        </tr>
        -->

        <tr>
          <th><img src="captcha.php" /></th>
          <th><input name="captcha" type="text" size="20" maxlength="20" /></th>
        </tr>
    
        <tr>
          <th colspan=2>
            <input type="hidden" name="language" value="ru">
            <input name="register" type="checkbox" value="1" /> {L_reg_i_agree} <a href="{C_url_rules}"><u><font color="red">{L_reg_with_rules}</font></u></a>
          </th>
        </tr>
      </tbody>
    </table>
    <input name="submit" type="submit" value="{L_signup}!" />
  </form><br>
  {L_log_reg_already} <a href="login.php{referral}"><u>{L_log_login_page}</u></a><br><br>

  <div id="log_menu">
    <a href="login.php{referral}">{L_log_login_page}</a> ::
    <!-- INCLUDE login_menu.tpl -->
  </div>
</div>
