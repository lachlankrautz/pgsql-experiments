<?php

use Faker\Provider\Base;

/**
 * Class PleaProvider
 */
class PleaProvider extends Base
{
    /**
     * @var array
     */
    protected static $pleas = [
        'Hey I\'ve got kids to feed',
        'I\'m going to sue',
        'Is this about my bonus?',
        'That wasn\'t my responsibility',
        'I\'ll be back',
        'I\m taking my laptop',
        'Can I still get a reference?',
        'I\'m the only one who knows how this works',
        'You can\'t replace me',
        'I\'m not signing anything',
        '(╯°□°)╯︵ ┻━┻',
        'I\'m going to the press',
        'I\'ll tell them everything',
        'You can\'t silence me',
        'Who\'s coming with me?',
        'You can\'t fire me, I quit!',
    ];

    /**
     * @return string
     */
    public function plea(): string
    {
        return static::randomElement(static::$pleas);
    }
}
