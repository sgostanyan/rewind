<?php

namespace Drupal\facebook_last_posts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Facebook;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;

/**
 * Provides a 'FacebookLastPostsBlock' block.
 *
 * @Block(
 *  id = "facebook_last_posts_block",
 *  admin_label = @Translation("Facebook last posts block"),
 * )
 */
class FacebookLastPostsBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'facebook_key' => $this->t('Facebook key'),
        'access_token' => $this->t('Access token')
      ] + parent::defaultConfiguration();

  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['facebook_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook Key'),
      '#description' => $this->t(''),
      '#default_value' => $this->configuration['facebook_key'],
      '#weight' => '0',
    ];
    $form['access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access token'),
      '#description' => $this->t(''),
      '#default_value' => $this->configuration['access_token'],
      '#weight' => '1',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['facebook_key'] = $form_state->getValue('facebook_key');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $content = file_get_contents('https://graph.facebook.com/v2.6/507421536093162/posts?fields=picture,message,created_time,source,target,from,name&limit=3&access_token=233712713654894|nEb2hU-wxVTmtpRsq9j_oG-pqJ8&debug=all&format=json&method=get&pretty=0&suppress_http_code=1');

    $datas = json_decode($content, true);
    $data = $datas["data"];
    $data['profile_picture'] = 'http://graph.facebook.com/507421536093162/picture?type=square';

    foreach ($data as $index => $value){

      if(gettype($value) == "array" && array_key_exists('created_time', $value)){
        $date = date("d/m/Y - h:m", strtotime($value["created_time"]));
        $data[$index]["created_time"] = $date;
      }
    }

    return array(
      '#theme' => 'facebook_last_posts',
      '#content' => $data,
      '#attached' => array(
        'library' => array(
          'facebook_last_posts/fblp-style',
        ),
      ),
    );
  }

}
