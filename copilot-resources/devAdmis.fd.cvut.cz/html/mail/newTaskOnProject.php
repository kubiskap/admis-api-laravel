<?php

// NEW TASK ON PROJECT MAIL
function prepareNewTaskOnProjectMail(int $idTask)
{
    require_once __DIR__ . "/../conf/config.inc";
    require_once SYSTEMINCLUDES . "function.php";
    require_once SYSTEMINCLUDES . "autoLoader.php";

    $taskDetails = getTaskDetails($idTask);
    $htmlMail = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="x-apple-disable-message-reformatting" />
<title>ADMIS nový úkol na tvém projektu</title>
<style type="text/css">
	body {
		margin: 0;
		background-color: #eee;
	}
	table {
		border-spacing: 0;
	}
	td {
		padding: 0;
	}
	img {
		border: 0;
	}
	.wrapper {
	    width: 100%;
	    table-layout: fixed;
	    background: #eee;
	    padding-bottom: 60px;
	}
	.main {
	    background: #eee;
	    margin: 0 auto;
	    width: 100%;
	    /* max-width: 600px; */
	    border-spacing: 0;
	    font-family: "Roboto Light", sans-serif;
	    color: #3C4858;
	}
	.main td {
	    padding: 0 1px;
	}
	.two-columns {
	    text-align: center;
	    font-size: 0;
	}
	.two-columns .column {
	    width: 100%;
	    max-width: 300px;
	    display: inline-block;
	    vertical-align: top;
	}
	.card {
        max-width: 500px; 
        min-width: 300px; 
        margin: 15px auto; 
        padding: 20px; 
        background-color: #fff;
        border-radius: 4px;
    }
    .table-striped tr:nth-child(even) {
        background-color: #eee;
    }
    .table-triped td{
        padding: 5px;
    }

</style>
</head>
<body>

<center class="wrapper">

<table class="main" width="100%">

<!-- TOP BORDER -->
<tr>
<td height="8" style="background-color: #d81b60"></td>
</tr>

<!-- LOGO SECTION -->
<tr style=" margin-bottom: 20px;">
<td style="padding: 14px 0 4px; background-color: #3C4858">
    <table width="100%">
    <tr>
    <td class="two-columns">
    <table class="column">
    <tr>
    <td style="padding: 0 62px 10px"><a href="https://admis.fd.cvut.cz"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACkAAAAmCAYAAABZNrIjAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkFCMThDMUY2RjMxRDExRThBMTgyRkJGN0JFRTY4MEZEIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkFCMThDMUY3RjMxRDExRThBMTgyRkJGN0JFRTY4MEZEIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QUIxOEMxRjRGMzFEMTFFOEExODJGQkY3QkVFNjgwRkQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QUIxOEMxRjVGMzFEMTFFOEExODJGQkY3QkVFNjgwRkQiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5QeF8tAAACcElEQVR42uyYz0tUURTH33NEC2RAXbSwKCuIwH7QIiJoUsisNhX0YxetWlghQYv+gGjlLiUioRIxSKWFRdEmqCgIKkqqTRBi5SJqEf3AsMbPgSM8Hu/NvPvuXGeGvPDhzbz7Y75z7r3nnHv9fD7vVXqp8aqgVIXI2grR0QArYBU0wwS8gdlyiZTZWwlbYRtsgbWwDDLa5hmch3H54i/QxsnCduiCDlgPdaE2IsSHd7Bx3oquLdmggo5Ap1oqrjyC6zCgYmddr0mxwnE4DMsTtP8Mu6HF9caRcfZBD7Qn9Brz0ytWmynUx1ZkPRyFs7DBoF8/rIE9Lv2kWOAQvNC1ZCLwHJyCXy79pLiOXtiRou8n6IswkOz0SeiGjzYis+q7TjqIVDLeb7hkY8ldcBlWV2Lszqj17qcU+B1uuYzdTTAEey3Gl13/BQ66sKS4h4eWAiV6XIElMfV/9Ck+8q+pJdfBXWi1nKWhGEP4+jwDN+FtQHAikZKN3NOUybYUy1wkHI6ZTncjjJRIoLPdLQ56c6UfH2aq4Yxz0SSeRpRvCdahtUjJiK+mHOeruhw/JhkJfvZt/eQF3XWmZRSextS9hymdpSfw09xPcMYJcSxvXjbB/tC7XGDMpdAMNRG/V5SoiDMI1wz+p1jnVYHI4mmGI0viXynD4mk9ViYpw64vGuIG/qGnvA9F+ku72+VM1SRTPgDTBdo81nZlzSdf62FpKqb+Toyr8dK4GpuDmAjNwcvQe0mvHoQ2h+ckghm4gizcCLiYCcgE6muhC3pgJ/hp3E0UaTqdUJEDpRJRjLQXVm16MfB8IRIMf/E6+n8SOSfAAOJw8c6Kft1qAAAAAElFTkSuQmCC" alt=""><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGoAAAAmCAYAAAAsuw6AAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkQ2NEMxMEFBRjMxRDExRThBMkVDRjVEREZBOUUxNDk2IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkQ2NEMxMEFCRjMxRDExRThBMkVDRjVEREZBOUUxNDk2Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RDY0QzEwQThGMzFEMTFFOEEyRUNGNURERkE5RTE0OTYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RDY0QzEwQTlGMzFEMTFFOEEyRUNGNURERkE5RTE0OTYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4n9TfwAAAE7klEQVR42uxaXYhVVRQ+d+5o2jXHsZoZRpuRtEQzCQLRrEbTGfNvTCNEkHkQgvRBIsLoB6KHnkYmevGhCJEhf0DB2ygilNIgDmYhqDU2/jQq6miTUzKa85O3teC7uFntc87e55x7MdgffIx3nb332Xd/e6291r6mcrmc5/DgI+WEckI5OKGcUA5OKAcnlBPKwQnlUFShniPOIQ6qY+Jvlvh7QN/5xKnEIb+5Ea8Re4ndxOsBY60kVhCHFVsp+uwN6NdAfFLMoYR4m7hb+V4TiMuU8UcQfyUe1ow5njibOIP4ONqm8A7mDeJlfKdOvCscLFQM7s/5482Qvnty5rhFPExcqhmHN9vJgH41Pu8vI1726deD5/m2DZo2OzVjNhFvWHyv9aZrXRLDm3iXzQ143pig5z9CnEfcR3xb7jXQr99in2c83kSfZ/cCxlTbSK/eBi8yxUjThnGEWkAsC3j+IsRMGp8QJ1m0f93SHhWbohw9pg1LY0xshfjM8TutvHwc8RViq8WYW3EuPUTM4AyrExtqLDZBt+GY3HYK8Zxie5RYn6BItcSZwsZnzxfEE8S/lfV+jFiOOXUWWih275eFrRXC1AoxTYXiUNNCPC3sXxHXCdtTFnMdTVxK/Fyx8TyrEhSKF/9hYTtIfCepF0QNffMwORVbiD8KW51lzNaFAl1mVWE531UFDnuxwlohhZKJwnmI9J1mp9XFnONNja08pA+nwAPKZy4hpisiL1Ce3QkpI0wwqEk+XkU0eZe4GonXZCQ4RRGqHKFDxSFl9w+GnGW2+Etjy4T0OSJCKNcyy/HvehENeIMdiznHKxqxOeSuJTYTd2JOvxDPEtuJawotFB/O1cJ2AH/P4PD0xHkwLsYi/KOxpUP68KLtF7bXlDRaxTfEWwl4/feG6Xgl8SXiduKGQgolPaQXu0WKlkc1xC0m0pobCb5FWUJ8QdRCbTb1TADeg7fY4H3TTWwr1BjiQmH7CTuyHGn10YSL35RBsSkxCp7dKWwt2NFq2OvCvOPiN5xDH8G7LhL7Q/pwwf1sIdLz2SL99jC5biH6PfG5HiL3R1gA3eF7x8CjPIS/aYp9qmiXTaDwlyH3U3AU6rUKCMLv3kh8QnPDk7hHNfp4WRUmlKccdxIyryjQhYY+w757Q86+tgKG37tIMk7gPZuJX0d1FhuhODwsijHxZYZFr8TzGluv4Tt/IP7s8+w48VSRz07deg8nHfpmEZ8Wtj9xVZLyKUrV8Rfh0B4MOIvWILaPxsaYhhpE4pzhnPlnBb7IfUbzLJuwCFxE808ml7AmfUotl8F3WavpdzVpoXQe0YSFyAivuIu0d4k4H2aJDFHiA8OQ0mEx7ywyMrmL9yUsFG+oNyz7XA/w+Eihr1Qser7Ia4cw/QpvI/7rduzyBBakBfWaKY57/70/PKaxxUWUX2D5EvqPJIViT5ghbO0+twZ5fKvJzhoj1iw51ChvET/UhMxUwPca1iQN2ZASoMQLv6uLmyny9dLHnoWnmMb6z7z71/Wceu4O6XMBIacG/UuwaBmcU3tQewwE3J9x0tCDWqfLpy2L+CXS3iHMrUOzc9PwdBZgl3i+AzXXANr1Kd81XyM1e/d/ste9oxlhvRJXVOOxvilcYQ0h4nThyu2ojRe6/9zyP4ETygnl4IRyQjk4oRycUE4oByeUgx3+FWAAXciSN87sWJ8AAAAASUVORK5CYII=" alt=""></a></td>
    </tr>
</table>
<table class="column">
    <tr>
    <td style="padding: 0 10px 10px; height: 38px; font-size: 16px; color: #fff;">Nový úkol na tvém projektu ID '.$taskDetails['idProject'].'</td>
    </tr>
</table>
</td>
    </tr>
</table>
</td>
</tr>';

    $htmlMail .= '
<tr>
<td>
<div class="card">
  <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">'.$taskDetails['name'].'</div>
  <br>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
  '.$taskDetails['description'].'
  </table>
  <br>
  <div style="padding-top: 16px; font-size: 14px; border-top: 1px solid #eee;">Termín: '.$taskDetails['deadline'].'<br>
  Stav: <b style="color: '.$taskDetails['statusColor'].'">⬤</b> '.$taskDetails['status'].'<br>
  Autor úkolu: '.getUserAll($taskDetails['createdBy'])[0]['name'].'
  </div>
</div>
</td>
</tr>';
    return $htmlMail;
}

?>