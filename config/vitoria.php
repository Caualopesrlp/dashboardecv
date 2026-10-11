<?php

$apiTeamId = (int) env('VITORIA_API_TEAM_ID', 1782);

return [
    'api_team_id' => $apiTeamId,
    'api_team_crest_url' => 'https://crests.football-data.org/'.$apiTeamId.'.png',
];
