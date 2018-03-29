<?php
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
namespace Thumber\View\Helper;

use Cake\View\Helper;
use Thumber\ThumbTrait;
use Thumber\Utility\ThumbCreator;

/**
 * Thumb Helper.
 *
 * This helper allows you to generate thumbnails.
 */
class ThumbHelper extends Helper
{
    use ThumbTrait;

    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Magic method.
     *
     * It dynamically calls `crop()`, `fit()`, `resize()` and `resizeCanvas()`
     *  methods.
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
     */
    public function __call($name, $params)
    {
        $methodToCall = sprintf('%sUrl', $name);
        if (method_exists($this, $methodToCall)) {
            $url = call_user_func_array([$this, $methodToCall], $params);

            return $this->Html->image($url, empty($params[2]) ? [] : $params[2]);
        }

        return parent::__call($name, $params);
    }

    /**
     * Creates a thumbnail, cutting out a rectangular part of the image, and
     *  returns its url.
     *
     * You can define optional coordinates to move the top-left corner of the
     *  cutout to a certain position.
     * @param string $path Path of the image from which to create the
     *  thumbnail. It can be a relative path (to APP/webroot/img), a full path
     *  or a remote url
     * @param array $params Parameters for creating the thumbnail
     * @param array $options Array of HTML attributes for the `img` element
     * @return string
     * @see https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper#crop-and-cropurl
     */
    public function cropUrl($path, array $params = [], array $options = [])
    {
        //Sets default parameters and options
        $params += ['format' => 'jpg', 'height' => null, 'width' => null];
        $options += ['fullBase' => true];

        //Creates the thumbnail
        $thumb = (new ThumbCreator($path))->crop($params['width'], $params['height'])->save($params);

        return $this->getUrl($thumb, $options['fullBase']);
    }

    /**
     * Creates a thumbnail, combining cropping and resizing to format image in
     *   a smart way, and returns its url.
     *
     * This method will find the best fitting aspect ratio on the current image
     *  automatically, cuts it out and resizes it to the given dimension.
     * @param string $path Path of the image from which to create the
     *  thumbnail. It can be a relative path (to APP/webroot/img), a full path
     *  or a remote url
     * @param array $params Parameters for creating the thumbnail
     * @param array $options Array of HTML attributes for the `img` element
     * @see https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper#fit-and-fiturl
     * @return string
     */
    public function fitUrl($path, array $params = [], array $options = [])
    {
        //Sets default parameters and options
        $params += ['format' => 'jpg', 'height' => null, 'width' => null];
        $options += ['fullBase' => true];

        //Creates the thumbnail
        $thumb = (new ThumbCreator($path))->fit($params['width'], $params['height'])->save($params);

        return $this->getUrl($thumb, $options['fullBase']);
    }

    /**
     * Creates a resized thumbnail and returns its url.
     * @param string $path Path of the image from which to create the
     *  thumbnail. It can be a relative path (to APP/webroot/img), a full path
     *  or a remote url
     * @param array $params Parameters for creating the thumbnail
     * @param array $options Array of HTML attributes for the `img` element
     * @return string
     * @see https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper#resize-and-resizeurl
     */
    public function resizeUrl($path, array $params = [], array $options = [])
    {
        //Sets default parameters and options
        $params += ['format' => 'jpg', 'height' => null, 'width' => null];
        $options += ['fullBase' => true];

        //Creates the thumbnail
        $thumb = (new ThumbCreator($path))->resize($params['width'], $params['height'])->save($params);

        return $this->getUrl($thumb, $options['fullBase']);
    }

    /**
     * Creates a resized canvas thumbnail and returns its url.
     * @param string $path Path of the image from which to create the
     *  thumbnail. It can be a relative path (to APP/webroot/img), a full path
     *  or a remote url
     * @param array $params Parameters for creating the thumbnail
     * @param array $options Array of HTML attributes for the `img` element
     * @return string
     * @see https://github.com/mirko-pagliai/cakephp-thumber/wiki/How-to-use-the-helper#resizecanvas-and-resizecanvasurl
     * @since 1.3.1
     */
    public function resizeCanvasUrl($path, array $params = [], array $options = [])
    {
        //Sets default parameters and options
        $params += ['format' => 'jpg', 'height' => null, 'width' => null];
        $options += ['fullBase' => true];

        //Creates the thumbnail
        $thumb = (new ThumbCreator($path))->resizeCanvas($params['width'], $params['height'])->save($params);

        return $this->getUrl($thumb, $options['fullBase']);
    }
}
