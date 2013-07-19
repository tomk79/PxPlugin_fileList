<?php
$this->load_px_class('/bases/pxcommand.php');

/**
 * PX Plugin "sitemapExcel"
 */
class pxplugin_fileList_register_pxcommand extends px_bases_pxcommand{

  private $command;

  private $path_data_dir;

  /**
   * コンストラクタ
   * @param $command = PXコマンド配列
   * @param $px = PxFWコアオブジェクト
   */
  public function __construct( $command , $px ){
    parent::__construct( $command , $px );
    $this->command = $this->get_command();
    $this->path_data_dir = $this->px->get_conf('paths.px_dir').'_sys/ramdata/plugins/sitemapExcel/';
    $this->start();
  }

  private $source;

  /**
   * 処理の開始
   */
  private function start(){

    // $path_work_dir = $this->px->get_conf('paths.px_dir');

    $brother = $this->px->site()->get_bros();
    $brother_array = array();
    foreach ($brother as $page) {
      array_push($brother_array, $this->px->theme()->mk_link($page));
    }

    $this->source .= '<ul style="margin-left:40px;">' . "\n";
    foreach ($brother_array as $value) {
      $this->source .= '<li>' . $value . '</li>' . "\n";
    }
    $this->recursion();
    $this->source .= '</ul>';

    if( $this->command[2] == 'export' ){
      return $this->page_export();
    }

    return $this->page_homepage();

  }


  private function recursion($page_id) {

    $children = $this->px->site()->get_children($page_id);
    $children_count = count($children);
    if (!$children_count) return;

    $this->source .= '<li>' . "\n";
    $this->source .= '<ul style="margin-left:40px;">' . "\n";
    foreach ($children as $child) {
      $this->source .= '<li>' . $this->px->theme()->mk_link($child) . '</li>' . "\n";
      $this->recursion($child, true);
    }
    $this->source .= '</ul>' . "\n";
    $this->source .= '</li>' . "\n";

  }


  /**
   * ホームページを表示する。
   */
  private function page_homepage(){

    //$src = $this->source;

    $src .= '<p>サイトマップからファイルリストを出力するプラグインです。</p>'."\n";
    $src .= '<div class="cols unit">'."\n";
    $src .= ' <div class="cols-col"><div class="cols-pad">'."\n";
    $src .= '   <h2>エクスポート</h2>'."\n";
    $src .= '   <p>プロジェクト「'.t::h($this->px->get_conf('project.name')).'」に現在登録されているサイトマップからファイルリストを出力できます。</p>'."\n";
    $src .= '   <form action="?" method="get" class="inline">'."\n";
    $src .= '     <p class="center"><input type="submit" value="エクスポートする" /></p>'."\n";
    $src .= '     <div><input type="hidden" name="PX" value="'.t::h(implode('.',array($this->command[0],$this->command[1],'export'))).'" /></div>'."\n";
    $src .= '   </form>'."\n";
    $src .= ' </div></div>'."\n";
    $src .= '</div><!-- / .cols -->'."\n";
    $src .= ''."\n";
    $src .= '<style type="text/css">ul li{list-style: none !important; margin-left: 20px}</style>';


    print $this->html_template($src);
    exit;
  }

  /**
   * 現在のサイトマップをエクスポートする。
   */
  private function page_export(){

    $this->px->dbh()->file_overwrite( './list.html' , $this->source , $perm = null );
    print '<a href="/list.html">ファイルリストへ</a>';

    exit;
  }

}

?>
