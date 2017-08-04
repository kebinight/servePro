<%
/**
* CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
* Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
* @link          http://cakephp.org CakePHP(tm) Project
* @since         0.1.0
* @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/
%>

/**
* Index method
*
* @return void
*/
public function index()
{
$this->set('<%= $pluralName %>', $this-><%= $currentModelName %>);
$this->set([
      'pageTitle'=>'<%= $pluralName %>管理',
      'bread'=>[
            'first'=>['name'=>'xxx'],
            'second'=>['name'=>'<%= $pluralName %>管理'],
        ],
      ]);
}