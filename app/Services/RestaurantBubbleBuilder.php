<?php

namespace App\Services;

use Illuminate\Support\Arr;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;

class RestaurantBubbleBuilder implements ContainerBuilder
{
    private $imageUrl;
    private $name;
    private $closestStation;
    private $minutesByFoot;
    private $category;
    private $budget;
    private $opentime;
    private $holiday;
    private $latitude;
    private $longitude;
    private $phoneNumber;
    private $restaurantUrl;
    private const GOOGLE_MAP_URL = 'https://www.google.com/maps';



    /**
     * プロパティをもとにバブルコンテナの連想配列を返す
     * @return RestaurantBubbleBuilder
     */
    public static function builder(): RestaurantBubbleBuilder
    {
        return new self();
    }



    /**
     * JSONを変換する
     * @return array
     */
    public function build(): array
    {
        $array = [
            'type' => 'bubble',
            'hero' => [
                'type' => 'image',
                'url' => $this->imageUrl,
                'size' => 'full',
                'aspectRatio' => '20:13',
                'aspectMode' => 'cover',
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => $this->name,
                        'wrap' => true,
                        'weight' => 'bold',
                        'size' => 'md',
                    ],
                    [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'margin' => 'lg',
                        'spacing' => 'sm',
                        'contents' => [
                            [
                                'type' => 'box',
                                'layout' => 'baseline',
                                'spacing' => 'xs',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'エリア',
                                        'color' => '#aaaaaa',
                                        'size' => 'xs',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $this->closestStation . '徒歩' . $this->minutesByFoot . '分',
                                        'wrap' => true,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ],
                            [
                                'type' => 'box',
                                'layout' => 'baseline',
                                'spacing' => 'xs',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'ジャンル',
                                        'color' => '#aaaaaa',
                                        'size' => 'xs',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $this->category,
                                        'wrap' => true,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ],
                            [
                                'type' => 'box',
                                'layout' => 'baseline',
                                'spacing' => 'xs',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => '予算',
                                        'wrap' => true,
                                        'color' => '#aaaaaa',
                                        'size' => 'sm',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => is_numeric($this->budget) ? '¥' . number_format($this->budget) . '円' : '不明',
                                        'wrap' => true,
                                        'maxLines' => 1,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ],
                            [
                                "type" => "box",
                                "layout" => "baseline",
                                "spacing" => "sm",
                                "contents" => [
                                    [
                                        'type' => 'text',
                                        'text' => '営業時間',
                                        'wrap' => true,
                                        'color' => '#aaaaaa',
                                        'size' => 'sm',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $this->opentime,
                                        'wrap' => true,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ],
                            [
                                "type" => "box",
                                "layout" => "baseline",
                                "spacing" => "sm",
                                "contents" => [
                                    [
                                        'type' => 'text',
                                        'text' => '休業日',
                                        'wrap' => true,
                                        'color' => '#aaaaaa',
                                        'size' => 'sm',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $this->holiday,
                                        'wrap' => true,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'footer' => [
                'type' => 'box',
                'layout' => 'vertical',
                'spacing' => 'xs',
                'contents' => [
                    [
                        'type' => 'button',
                        'style' => 'link',
                        'height' => 'sm',
                        'action' => [
                            'type' => 'uri',
                            'label' => '地図を見る',
                            'uri' => self::GOOGLE_MAP_URL . '?q=' . $this->latitude . ',' . $this->longitude,
                        ]
                    ],
                    [
                        'type' => 'button',
                        'style' => 'link',
                        'height' => 'sm',
                        'action' => [
                            'type' => 'uri',
                            'label' => '詳しく見る',
                            'uri' => $this->restaurantUrl,
                        ]
                    ],
                    [
                        'type' => 'spacer',
                        'size' => 'xs'
                    ]
                ],
                'flex' => 0
            ]
        ];
        
        if ($this->imageUrl == '') {
            Arr::forget($array, 'hero');
        }
        
        return $array;
    }



    /**
     * ぐるなびAPIの検索結果をプロパティに代入する
     * @param array $restaurant 飲食店1件分の情報の入った連想配列
     */
    public function setContents(array $restaurant): void
    {
        $this->imageUrl       = Arr::get($restaurant, 'image_url.shop_image1', null);
        $this->name           = Arr::get($restaurant, 'name', null);
        $this->closestStation = Arr::get($restaurant, 'access.station', null);
        $this->minutesByFoot  = Arr::get($restaurant, 'access.walk', null);
        $this->category       = Arr::get($restaurant, 'category', null);
        $this->budget         = Arr::get($restaurant, 'budget', null);
        $this->opentime       = Arr::get($restaurant, 'opentime', null);
        $this->holiday        = Arr::get($restaurant, 'holiday', null);
        $this->latitude       = Arr::get($restaurant, 'latitude', null);
        $this->longitude      = Arr::get($restaurant, 'longitude', null);
        $this->phoneNumber    = Arr::get($restaurant, 'tel', null);
        $this->restaurantUrl  = Arr::get($restaurant, 'url', null);
    }
}
