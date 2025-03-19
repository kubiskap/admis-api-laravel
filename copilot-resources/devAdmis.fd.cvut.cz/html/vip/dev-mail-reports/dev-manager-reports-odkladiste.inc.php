<!-- ROZLOŽENÍ PROJEKTŮ MEZI EDITORY -->
<tr>
    <td>
        <div class="card">
            <div style="padding-bottom: 16px; font-size: 18px; border-bottom: 1px solid #eee;">Rozložení projektů mezi editory</div>
            <br/>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">';
                $editor2Project = countPhasesToEditors();
                $sumOfAllProjects;
                foreach ($editor2Project as $editorStats) {
                $htmlMail .= '
                <tr>
                    <td align="right" style="padding: 4px 16px 4px 0;">
                        <table width="'.round(5*100*$editorStats['countProjektu']/$sumOfAllProjects).'%" cellpadding="0" cellspacing="0" border="0"> <!-- 3.09 / 3.708 -->
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <td bgcolor="#e91e63">&nbsp;</td>
                                </table>
                            </td>
                        </table>
                    </td>
                    <td width="72%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <td valign="baseline">'.getUserAll($editorStats['editor'])[0]['name'].'</td>
                            <td width="17%" valign="baseline" style="text-align: right">'.$editorStats['countProjektu'].'</td>
                            <td width="17%" style="font-size: 12px; color: #868E96; text-align: right" valign="baseline">'.round(100*$editorStats['countProjektu']/$sumOfAllProjects, 1).'%</td>
                        </table>
                    </td>
                </tr>';
                }
                $htmlMail .= '
            </table>
        </div>
    </td>
</tr>