<?php

namespace App\DataFixtures;

use DateInterval;
use DateTimeImmutable;

/**
 * @codeCoverageIgnore
 */
class DateFixtures
{
    /**
     * Function to simulate a date 
     *
     * @return DateTimeImmutable
     */
    public function randDate(): DateTimeImmutable
    {
        // Date de début et de fin de la fourchette.
        $startDate = new DateTimeImmutable('2023-01-01');
        $endDate = new DateTimeImmutable('2024-12-31');

        // Calcul de la différence entre les deux dates.
        $interval = $endDate->diff($startDate);

        // Génération d'un nombre aléatoire de jours entre 0 et le nombre total de jours dans la période.
        $randomDays = rand(0, $interval->days);

        // Ajout du nombre de jours aléatoires à la date de début.
        $randomDate = $startDate->add(new DateInterval('P'.$randomDays.'D'));

        return $randomDate;
        
    }
}
