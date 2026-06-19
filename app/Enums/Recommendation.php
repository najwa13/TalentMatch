<?php

namespace App\Enums;

enum Recommendation: string
{
    case Convocation = 'convoquer';
    case Attente = 'attente';
    case Rejet = 'rejeter';
}
