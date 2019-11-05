<?php

ini_set('display_errors','On');
error_reporting('E_ALL');

    function cmp_price($a, $b)
    {
        if ($a["price"] == $b["price"]) {
            return 0;
        }
        return ($a["price"] < $b["price"]) ? -1 : 1;
    }

	function declension($num,$expr)
	{
		$return="";
		$last=0;

		$num=preg_replace("/[^0-9]/","",$num);

		if ($num>=5 && $num<=20)	$return=$expr[2];
		else
		{
			$last=substr($num,-1);

			if ($last==1)	$return=$expr[0];
			elseif ($last>=2 && $last<=4)	$return=$expr[1];
			else	$return=$expr[2];
		}

		return	$return;
	}

    $url = "https://www.sknt.ru/job/frontend/data.json";

    $json = file_get_contents($url);

    if($json == false) exit("ERROR 1");

    $data_ar = json_decode($json, true);

    $this_page = 0;

    if(isset($_POST["page"]) and is_numeric($_POST["page"]))
    {    	$this_page = $_POST["page"];    }

    $title = "Тарифы";
    $html = "";

	switch($this_page)
	{
        case 2:

    		    $block_tmpl = file_get_contents("tmpl/block2.tmpl");
    		    $h1_tmpl = file_get_contents("tmpl/h1_2.tmpl");

                $page_id1 = 1;
                $page_id2 = 3;

        		if(isset($_POST["id"]) and $_POST["id"] != "")
        		{

                   $ar_tarifs = $data_ar["tarifs"][$_POST["id"]]["tarifs"];

                   usort($ar_tarifs, "cmp_price");

                   $first = $ar_tarifs[0]["price"];

                	foreach($ar_tarifs as $k=>$v)
                	{
                    	     $price_one = "разовый платеж &mdash; ".$v["price"];

							 $expr = array("месяц", "месяца", "месяцев");

                    	     $pay_period = $v["pay_period"]." ".declension($v["pay_period"], $expr);

                             $discount = "";

                             $price = $v["price"]." <i class='fa fa-rub'></i>/мес";

                    	     if($first != $v["price"])
                    	     {                    	     	$discount = $first*$v["pay_period"]-$v["price"];

                    	     	$price = ($v["price"]/$v["pay_period"])." <i class='fa fa-rub'></i>/мес";

                    	     	$price_one .= " <i class='fa fa-rub'></i><p>скидка &mdash; ".$discount." <i class='fa fa-rub'></i></p>";
                    	     }


                    	     $html .= str_replace(array("{ID}", "{PAY_PERIOD}", "{PRICE}", "{PRICE_ONE}", "{TITLE}"),
                    	         array($page_id2."_".$v["ID"], $pay_period, $price,
                    	         "<div class='price_one'>".$price_one."</div>", $v["title"]),
                    	         $block_tmpl
                    	     );                	}

        		}

        		$h1 = "Тариф \"".$data_ar["tarifs"][$_POST["id"]]["title"]."\"";

        		echo str_replace(array("{H1}", "{PAGEID1}"), array($h1, $page_id1), $h1_tmpl).$html;

        break;

        case 3:

    		    $block_tmpl = file_get_contents("tmpl/block3.tmpl");

                $page_id_back = "";

        		if(isset($_POST["id"]) and $_POST["id"] != "")
        		{
                    $this_data_ar = array();

                    $parent_id = "";

                    foreach($data_ar["tarifs"] as $k=>$v)
                    {
                    	$parent_id = $k;

                   		foreach($v["tarifs"] as $k2=>$v2)
                   		{                    		if($v2["ID"] == $_POST["id"])
                    		{                    	        $this_data_ar = $v2;
                    	 		break 2;                    		}                   		}
                    }

                    $ar_tarifs = $data_ar["tarifs"][$parent_id]["tarifs"];

                    usort($ar_tarifs, "cmp_price");

                    $first = $ar_tarifs[0]["price"];

					$expr = array("месяц", "месяца", "месяцев");

                    $pay_period = "Период оплаты &mdash; ".$this_data_ar["pay_period"]." ".
                    declension($this_data_ar["pay_period"], $expr)."</br>".$first." <i class='fa fa-rub'></i>/мес";


                   $timestamp = explode("+", $this_data_ar["new_payday"]);
                   $timestamp = $timestamp[0];

                   $date = date("d.m.Y", $timestamp);

                   $date = "Вступит в силу &mdash; сегодня</br>активно до ".$date."";

                   $price = "Разовай платеж &mdash; ".$this_data_ar["price"]." <i class='fa fa-rub'></i></br>Со счета спишется &mdash; "
                   .$this_data_ar["price"]." <i class='fa fa-rub'></i>";

                   $html .= str_replace(array("{PAGEID2}", "{PAY_PERIOD}", "{PRICE}", "{DATE}", "{TITLE}"),
                          array("2_".$parent_id, $pay_period, $price,
                          $date, "Тариф \"".$v["title"]."\""),
                          $block_tmpl
                   );

                   echo $html;
				}


        break;

        default:

            $page_id = 2;

            $page_tmpl = file_get_contents("tmpl/page.tmpl");

    		$block_tmpl = file_get_contents("tmpl/block1.tmpl");

    		$i = 1;
    		$n = 4;

            foreach($data_ar["tarifs"] as $k=>$v)
            {
    	        if($i%$n == 0) $i = 1;

    	        $class = " speed1bgcolor".$i;

    	        $ar_tarifs = $v["tarifs"];

                usort($ar_tarifs, "cmp_price");

        	    $url = str_replace("http://www.sknt.ru", "", $v["link"]);

        	    $free_options = "";

        	    if(isset($v["free_options"]) and is_array($v["free_options"]))
        	    {
        	        foreach($v["free_options"] as $k1=>$v1)
        	        {
        	            $free_options .= "<p>".$v1."</p>";
        	        }

        	        if($free_options != "") $free_options = "<div class='block1free'>".$free_options."</div>";
        	    }

    		    $html .= str_replace(
    	    	    array("{ID}", "{TITLE}", "{SPEED}", "{PRICE}", "{URL}", "{FREE}", "{CLASS}"),
    	    	    array($page_id."_".$k, "Тариф \"".$v["title"]."\"", $v["speed"]." Мбит/с",
    	        	    $ar_tarifs[0]["price"]." - ".$ar_tarifs[count($ar_tarifs)-1]["price"]." <i class='fa fa-rub'></i>/мес",
    	        	    $url, $free_options, $class),
    	    	    $block_tmpl
    		    );

    		    $i++;
    	    }

    	    echo str_replace(array("{TITLE}", "{CONTENT}"), array($title, $html), $page_tmpl); //только для 1-й стр - default
    }


?>