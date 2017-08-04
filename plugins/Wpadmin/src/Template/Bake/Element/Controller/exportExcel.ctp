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
$columnArr = $modelObj->schema()->columns();

foreach($columnArr as $key=>$field){
 if($field==$modelObj->primaryKey()){
    continue;
 }
   $fieldData =$modelObj->schema()->column($field);
  $colName = $fieldData['comment']?$fieldData['comment']:$field;
  $csvColumnArr[] = "'".$colName."'";
  $fieldArr[] = "'".$field."'";
}
$csvColumnStr = '['.implode(',',$csvColumnArr).']';
$fieldStr = '['.implode(',',$fieldArr).']';
%>

/**
* export csv
*
* @return csv 
*/
public function exportExcel()
{
        $sort = $this->request->query('sidx');
        $order = $this->request->query('sort');
        $keywords = $this->request->query('keywords');
        $begin_time = $this->request->query('begin_time');
        $end_time = $this->request->query('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where['username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`create_time`) >' => $begin_time], ['date(`create_time`) <' => $end_time]];
        }
        $Table =  $this-><%= $currentModelName %>;
        $column = <%=$csvColumnStr%>;
        $query = $Table->find();
        $query->hydrate(false);
        $query->select(<%=$fieldStr%>);
         if (!empty($where)) {
            $query->where($where);
        }
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }
        $res = $query->toArray();
        $this->autoRender = false;
        $filename = '<%=$currentModelName%>_'.date('Y-m-d').'.csv';
        \Wpadmin\Utils\Export::exportCsv($column,$res,$filename);

}
