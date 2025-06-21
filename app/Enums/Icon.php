<?php

declare(strict_types=1);

namespace App\Enums;

enum Icon: string
{
    case Home = 'house';
    case Wallet = 'wallet';
    case Folder = 'folder';
    case Book = 'book';
    case Bell = 'bell';
    case Cog = 'cog';
    case Star = 'star';
    case Music = 'music';
    case Shopping = 'shopping-basket';
    case Travel = 'plane';
    case Money = 'banknote';
    case Bank = 'landmark';
    case PiggyBank = 'piggy-bank';
    case Pizza = 'pizza';
    case Coffee = 'coffee';

    case GitHub = 'github';
    case Twitch = 'twitch';
    case YouTube = 'youtube';
    case Video = 'film';
    case TV = 'tv-minimal-play';
    case Phone = 'phone';
    case Zap = 'zap';
    case Mail = 'mail';
    case A11y = 'accessibility';
    case Camera = 'camera';
}
