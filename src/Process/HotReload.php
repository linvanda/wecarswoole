<?php

namespace WecarSwoole\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\File;
use Swoole\Table;
use Swoole\Timer;

/**
 * 热重载
 * Class HotReload
 * @package WecarSwoole\Process
 */
class HotReload extends AbstractProcess
{
    /** @var \swoole_table $table */
    protected $table;
    protected $isReady = false;

    protected $monitorDirs; // 需要监控的目录
    protected $monitorExt; // 需要监控的后缀

    /**
     * 启动定时器进行循环扫描
     */
    public function run($arg)
    {
        // 此处指定需要监视的目录 建议只监视App目录下的文件变更
        $this->monitorDirs = !empty($arg['monitorDirs']) ? $arg['monitorDirs'] : [EASYSWOOLE_ROOT . '/app'];

        // 指定需要监控的扩展名 不属于指定类型的的文件 无视变更 不重启
        $this->monitorExt = !empty($arg['monitorExt']) && is_array($arg['monitorExt']) ? $arg['monitorExt'] : ['php'];

        if (extension_loaded('inotify') && empty($arg['disableInotify'])) {
            // 扩展可用 优先使用扩展进行处理
            $this->registerInotifyEvent();
            echo "server hot reload start : use inotify\n";
        } else {
            // 扩展不可用时 进行暴力扫描
            $this->table = new Table(512);
            $this->table->column('mtime', Table::TYPE_INT, 4);
            $this->table->create();
            $this->runComparisions();
            Timer::tick(1000, function () {
                $this->runComparisions();
            });
            echo "server hot reload start : use timer tick comparison\n";
        }
    }

    private function runComparisions()
    {
        $inodeList = [];
        $doReload = false;
        foreach ($this->monitorDirs as $dir) {
            $doReload = $this->runComparison($dir, $inodeList);
        }

        if (!$doReload) {
            foreach ($this->table as $inode => $value) {
                // 迭代table寻找需要删除的inode
                if (!in_array(intval($inode), $inodeList)) {
                    $this->table->del($inode);
                    $doReload = true;
                }
            }
        }

        if ($doReload) {
            $this->reload();
        }
    }

    /**
     * 扫描文件变更
     */
    private function runComparison(string $dir, &$inodeList)
    {
        $startTime = microtime(true);
        $doReload = false;

        $dirIterator = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($dirIterator);

        // 迭代目录全部文件进行检查
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $ext = $file->getExtension();
            if (!in_array($ext, $this->monitorExt)) {
                continue; // 只检查指定类型
            } else {
                // 由于修改文件名称 并不需要重新载入 可以基于inode进行监控
                $inode = $file->getInode();
                $mtime = $file->getMTime();
                array_push($inodeList, $inode);
                if (!$this->table->exist($inode)) {
                    // 新建文件或修改文件 变更了inode
                    $this->table->set($inode, ['mtime' => $mtime]);
                    $doReload = true;
                } else {
                    // 修改文件 但未发生inode变更
                    $oldTime = $this->table->get($inode)['mtime'];
                    if ($oldTime != $mtime) {
                        $this->table->set($inode, ['mtime' => $mtime]);
                        $doReload = true;
                    }
                }
            }
        }

        return $doReload;
    }

    private function reload()
    {
        $startTime = microtime(true);
        $count = $this->table->count();
        $time = date('Y-m-d H:i:s');
        $usage = round(microtime(true) - $startTime, 3);
        if (!$this->isReady == false) {
            // 监测到需要进行热重启
            echo "severReload at {$time} use : {$usage} s total: {$count} files\n";
            ServerManager::getInstance()->getSwooleServer()->reload();
        } else {
            // 首次扫描不需要进行重启操作
            echo "hot reload ready at {$time} use : {$usage} s total: {$count} files\n";
            $this->isReady = true;
        }
    }

    /**
     * 注册Inotify监听事件
     */
    private function registerInotifyEvent()
    {
        // 因为进程独立 且当前是自定义进程 全局变量只有该进程使用
        // 在确定不会造成污染的情况下 也可以合理使用全局变量
        global $lastReloadTime;
        global $inotifyResource;

        $lastReloadTime = 0;
        $files = [];
        foreach ($this->monitorDirs as $dir) {
            $tmpFiles = File::scanDirectory($dir);
            $files = array_merge($files, $tmpFiles['files'], $tmpFiles['dirs']);
        }

        $inotifyResource = inotify_init();

        // 为当前所有的目录和文件添加事件监听
        foreach ($files as $item) {
            inotify_add_watch($inotifyResource, $item, IN_CREATE | IN_DELETE | IN_MODIFY);
        }

        // 加入事件循环
        swoole_event_add($inotifyResource, function () {
            global $lastReloadTime;
            global $inotifyResource;
            $events = inotify_read($inotifyResource);
            if ($lastReloadTime < time() && !empty($events)) { // 限制1s内不能进行重复reload
                $lastReloadTime = time();
                ServerManager::getInstance()->getSwooleServer()->reload();
            }
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
    }
}
