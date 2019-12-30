<?php
declare(strict_types=1);
/**
 * This file is part of cakephp-thumber.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-thumber
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @see         https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper
 */
namespace Thumber\Cake\View\Helper;

use BadMethodCallException;
use Cake\View\Helper;
use InvalidArgumentException;
use Thumber\Cake\Utility\ThumbCreator;

/**
 * Thumb Helper.
 *
 * This helper allows you to generate thumbnails.
 * @method string crop($path, array $params = [], array $options = []) Crops the image, cutting out a rectangular part of the image
 * @method string cropUrl($path, array $params = [], array $options = []) As for the `crop()` method, but this only returns the url
 * @method string fit($path, array $params = [], array $options = []) Resizes the image, combining cropping and resizing to format image in a smart way. It will find the best fitting aspect ratio on the current image automatically, cut it out and resize it to the given dimension
 * @method string fitUrl($path, array $params = [], array $options = []) As for the `fit()` method, but this only returns the url
 * @method string resize($path, array $params = [], array $options = []) Resizes the image
 * @method string resizeUrl($path, array $params = [], array $options = []) As for the `resize()` method, but this only returns the url
 * @method string resizeCanvas($path, array $params = [], array $options = []) Resizes the boundaries of the current image to given width and height. An anchor can be defined to determine from what point of the image the resizing is going to happen. Set the mode to relative to add or subtract the given width or height to the actual image dimensions. You can also pass a background color for the emerging area of the image
 * @method string resizeCanvasUrl($path, array $params = [], array $options = []) As for the `resizeCanvas()` method, but this only returns the url
 */
class ThumbHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Magic method. It dynamically calls all other methods.
     *
     * Each method takes these arguments:
     *  - $path Path of the image from which to create the thumbnail. It can be
     *      a relative path (to APP/webroot/img), a full path or a remote url;
     *  - $params Parameters for creating the thumbnail;
     *  - $options Array of HTML attributes for the `img` element.
     * @param string $name Method to invoke
     * @param array $params Array of params for the method
     * @return string
     * @see https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper
     * @since 1.4.0
     * @throws \InvalidArgumentException
     * @uses isUrlMethod()
     * @uses runUrlMethod()
     */
    public function __call(string $name, array $params): string
    {
        [$path, $params, $options] = $params + [null, [], []];
        is_true_or_fail($path, __d('thumber', 'Thumbnail path is missing'), InvalidArgumentException::class);
        $url = $this->runUrlMethod($name, $path, $params, $options);

        return $this->isUrlMethod($name) ? $url : $this->Html->image($url, $options);
    }

    /**
     * Checks is a method name is an "Url" method.
     *
     * This means that the last characters of the method name are "Url".
     *
     * Example: `cropUrl` is an "Url" method. `crop` is not.
     * @param string $name Method name
     * @return bool
     * @since 1.4.0
     */
    protected function isUrlMethod(string $name): bool
    {
        return string_ends_with($name, 'Url');
    }

    /**
     * Runs an "Url" method and returns the url generated by the method
     * @param string $name Method name
     * @param string $path Path of the image from which to create the thumbnail.
     *  It can be a relative path (to APP/webroot/img), a full path or a remote
     *  url
     * @param array $params Parameters for creating the thumbnail
     * @param array $options Array of HTML attributes for the `img` element
     * @return string Thumbnail url
     * @since 1.4.0
     * @throws \BadMethodCallException
     * @uses isUrlMethod()
     */
    protected function runUrlMethod(string $name, string $path, array $params = [], array $options = []): string
    {
        $name = $this->isUrlMethod($name) ? substr($name, 0, -3) : $name;
        $params += ['format' => 'jpg', 'height' => null, 'width' => null];
        $options += ['fullBase' => true];

        $className = ThumbCreator::class;
        is_true_or_fail(
            method_exists($className, $name),
            __d('thumber', 'Method `{0}::{1}()` does not exist', $className, $name),
            BadMethodCallException::class
        );
        $thumber = new $className($path);
        $thumber->$name($params['width'], $params['height'])->save($params);

        return $thumber->getUrl($options['fullBase']);
    }
}
