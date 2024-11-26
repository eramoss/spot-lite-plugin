<?php
class Spot_Lite_HTML_Exporter
{
    public function mount($template, $title, $description, $photo_urls, $activities)
    {
        $photos = '';
        foreach ($photo_urls as $photo_url) {
            $photos .= '
            <div class="image-container">
                <img src="' . $photo_url . '" alt="Foto do encontro">
            </div>';
        }

        $activities_html = '';
        foreach ($activities as $activity) {
            $participant_name = $activity['participant_name'];
            $participant_age = $activity['participant_age'];
            $desc = $activity['description'];
            $activities_html .= "
                <tr>
                    <td class='tb_est'>$participant_name</td>
                    <td class='tb_idd'>$participant_age</td>
                    <td class='tb_reg'>$desc</td>
                </tr>";
        }

        $html = str_replace('{{title}}', $title, $template);
        $html = str_replace('{{description}}', $description, $html);
        $html = str_replace('{{photos}}', $photos, $html);
        $html = str_replace('{{activities}}', $activities_html, $html);

        return $html;
    }
}
?>