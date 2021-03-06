<?php 
	//Script laden zodat je nooit pagina buiten de index om kan laden
	include("includes/security.php");

	//Als je geen pokemon bij je hebt, terug naar index.
	if($gebruiker['in_hand'] < 2) header('Location: index.php');

	$page = 'modify-order';
	//Goeie taal erbij laden voor de page
	include_once('language/language-pages.php');
?>

<center>
<div style="padding-bottom:10px;"><?php echo $txt['modify_order_text_old']; ?></div>
<div style="padding-bottom:10px;"><a href="?page=modify-order"><b>terug</b></a></div>
<div id="listContainer">
<?
  //Load User Pokemon
  
  if(isset($_POST['wat'])){
    if(($_POST['teller'] > 1) AND ($_POST['teller'] < 6)){
      if($_POST['wat'] == "down"){
        $anderepokemon = $_POST['teller']+1;
        //andere pokemon plekje omhoog zetten
        mysql_query("UPDATE `pokemon_speler` SET `opzak_nummer`=`opzak_nummer`-'1' WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='ja' AND `opzak_nummer`='".$anderepokemon."'");
        //Aangewezen pokemon 1 omlaag zetten
        mysql_query("UPDATE `pokemon_speler` SET `opzak_nummer`=`opzak_nummer`+'1' WHERE `id`='".$_POST['pokemonid']."'");
      }
      elseif($_POST['wat'] == "up"){
        $anderepokemon = $_POST['teller']-1;
        //andere pokemon plekje omhoog zetten
        mysql_query("UPDATE `pokemon_speler` SET `opzak_nummer`=`opzak_nummer`+'1' WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='ja' AND `opzak_nummer`='".$anderepokemon."'");
        //Aangewezen pokemon 1 omlaag zetten
        mysql_query("UPDATE `pokemon_speler` SET `opzak_nummer`=`opzak_nummer`-'1' WHERE `id`='".$_POST['pokemonid']."'");
      }
      //Load New info
      $pokemon_sql = mysql_query("SELECT pw.wereld, pw.naam, pw.type1, pw.type2, pw.zeldzaamheid, pw.groei, pw.aanval_1, pw.aanval_2, pw.aanval_3, pw.aanval_4, ps.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler AS ps ON ps.wild_id = pw.wild_id WHERE ps.user_id='".$_SESSION['id']."' AND ps.opzak='ja' ORDER BY ps.opzak_nummer ASC");
    }
  }

  $aantal = mysql_num_rows($pokemon_sql);
  $teller = 0;

  while($pokemon = mysql_fetch_array($pokemon_sql)){
    //Gegevens juist laden voor de pokemon
    $pokemon = pokemonei($pokemon);

    //Naam veranderen als het male of female is.
    $pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam']);

    //Default no shiny
    $shinyimg = 'pokemon';
    $shinystar = '';

    //Shiny?
    if($pokemon['shiny'] == 1){
      $shinyimg = 'shiny';
      $shinystar = '<img src="images/icons/lidbetaald.png" width="16" height="16" style="margin-bottom:-3px;" border="0" alt="Shiny" title="Shiny">';
    }

    //Teller elke keer met 1 verhogen
    $teller++;

    //Beeldweergave
    echo'<div id="item_'.$pokemon['id'].'" class="modify-order-item-old">
          <table>
            <tr><td colspan="2"><center><img src="'.$pokemon['animatie'].'" alt="" /></center></td></tr>
            <tr><td colspan="2"><center>'.$pokemon['naam'].$shinystar.'</center></td></tr>
            <tr><td colspan="2"><center>Lvl '.$pokemon['level'].'</center></td></tr>';
  	if($pokemon['ei'] != 1) echo'<tr><td colspan="2"><center>'.htmlspecialchars_decode($pokemon['type']).'</center></td></tr>';
  	else echo'<tr><td colspan="2"><center>??</center></td></tr>';

    if($aantal != 1){
      if($teller == 1){
        echo'
        <tr>
          <td colspan="2"><center>
            <form method="post" name="form1">
            <input type="image" onClick="form1.submit();" src="../images/icons/down.png" alt="" width="16" height="16" />
            <input type="hidden" value="'.$pokemon['id'].'" name="pokemonid">
            <input type="hidden" value="down" name="wat">
            <input type="hidden" value="'.$teller.'" name="teller">
            </form>
            <center>
          </td>
        </tr>'; 
      }
      elseif($teller == $aantal){
        echo'
        <tr>
          <td colspan="2"><center><form method="post" name="form1">
          <input type="image" onClick="form1.submit();" src="../images/icons/up.png" alt="" width="16" height="16" />
          <input type="hidden" value="'.$pokemon['id'].'" name="pokemonid">
          <input type="hidden" value="up" name="wat">
          <input type="hidden" value="'.$teller.'" name="teller">
          </form></center>
          </td>
        </tr>';
      }
      else{
        echo '
        <tr>
          <td><div align="right" style="padding-right:10px;">
          <form method="post" name="form1">
          <input type="image" onClick="form1.submit();" src="../images/icons/up.png" alt="" width="16" height="16" />
          <input type="hidden" value="'.$pokemon['id'].'" name="pokemonid">
          <input type="hidden" value="up" name="wat">
          <input type="hidden" value="'.$teller.'" name="teller">
          </form></div>
          </td>
          
          <td><div align="left" style="padding-left:10px;"><form method="post" name="form1">
          <input type="image" onClick="form1.submit();" src="../images/icons/down.png" alt="" width="16" height="16" />
          <input type="hidden" value="'.$pokemon['id'].'" name="pokemonid">
          <input type="hidden" value="down" name="wat">
          <input type="hidden" value="'.$teller.'" name="teller">
          </form></div>
          </td>
        </tr>';
      }
    }

echo '</table></div>';

}
mysql_data_seek($pokemon_sql, 0);
?>
</div>
</center>