<?php

namespace app\data\service;

use think\admin\Service;

/**
 * 用户提现数据服务
 * Class UserTransferService
 * @package app\data\service
 */
class UserTransferService extends Service
{
    /**
     * 提现方式配置
     * @var array
     */
    protected $types = [
        'wechat_wallet'  => '转账到我的微信零钱',
        'wechat_banks'   => '转账到我的银行卡账户',
        'wechat_qrcode'  => '线下转账到微信收款码',
        'alipay_qrcode'  => '线下转账到支付宝收款码',
        'alipay_account' => '线下转账到支付宝账户',
        'transfer_banks' => '线下转账到银行卡账户',
    ];

    /**
     * 微信提现银行
     * @var array
     */
    protected $banks = [
        '1002' => '工商银行',
        '1005' => '农业银行',
        '1003' => '建设银行',
        '1026' => '中国银行',
        '1020' => '交通银行',
        '1001' => '招商银行',
        '1066' => '邮储银行',
        '1006' => '民生银行',
        '1010' => '平安银行',
        '1021' => '中信银行',
        '1004' => '浦发银行',
        '1009' => '兴业银行',
        '1022' => '光大银行',
        '1027' => '广发银行',
        '1025' => '华夏银行',
        '1056' => '宁波银行',
        '4836' => '北京银行',
        '1024' => '上海银行',
        '1054' => '南京银行',
        '4755' => '长子县融汇村镇银行',
        '4216' => '长沙银行',
        '4051' => '浙江泰隆商业银行',
        '4753' => '中原银行',
        '4761' => '企业银行（中国）',
        '4036' => '顺德农商银行',
        '4752' => '衡水银行',
        '4756' => '长治银行',
        '4767' => '大同银行',
        '4115' => '河南省农村信用社',
        '4150' => '宁夏黄河农村商业银行',
        '4156' => '山西省农村信用社',
        '4166' => '安徽省农村信用社',
        '4157' => '甘肃省农村信用社',
        '4153' => '天津农村商业银行',
        '4113' => '广西壮族自治区农村信用社',
        '4108' => '陕西省农村信用社',
        '4076' => '深圳农村商业银行',
        '4052' => '宁波鄞州农村商业银行',
        '4764' => '浙江省农村信用社联合社',
        '4217' => '江苏省农村信用社联合社',
        '4072' => '江苏紫金农村商业银行股份有限公司',
        '4769' => '北京中关村银行股份有限公司',
        '4778' => '星展银行（中国）有限公司',
        '4766' => '枣庄银行股份有限公司',
        '4758' => '海口联合农村商业银行股份有限公司',
        '4763' => '南洋商业银行（中国）有限公司',
    ];

    /**
     * 获取微信提现银行
     * @param string|null $name
     * @return array|string
     */
    public function banks(?string $name = null)
    {
        return is_null($name) ? $this->banks : $this->banks[$name] ?? $name;
    }

    /**
     * 同步刷新用户返利
     * @param integer $uuid
     * @return array [total, count, audit, locks]
     */
    public function amount(int $uuid): array
    {
        if ($uuid > 0) {
            $locks = abs($this->app->db->name('DataUserTransfer')->whereRaw("uid='{$uuid}' and status=3")->sum('amount'));
            $total = abs($this->app->db->name('DataUserTransfer')->whereRaw("uid='{$uuid}' and status>=1")->sum('amount'));
            $count = abs($this->app->db->name('DataUserTransfer')->whereRaw("uid='{$uuid}' and status>=4")->sum('amount'));
            $audit = abs($this->app->db->name('DataUserTransfer')->whereRaw("uid='{$uuid}' and status>=1 and status<3")->sum('amount'));
        } else {
            $locks = abs($this->app->db->name('DataUserTransfer')->whereRaw("status=3")->sum('amount'));
            $total = abs($this->app->db->name('DataUserTransfer')->whereRaw("status>=1")->sum('amount'));
            $count = abs($this->app->db->name('DataUserTransfer')->whereRaw("status>=4")->sum('amount'));
            $audit = abs($this->app->db->name('DataUserTransfer')->whereRaw("status>=1 and status<3")->sum('amount'));
        }
        return [$total, $count, $audit, $locks];
    }

    /**
     * 获取转账类型
     * @param string|null $name
     * @return array|string
     */
    public function types(?string $name = null)
    {
        return is_null($name) ? $this->types : ($this->types[$name] ?? $name);
    }

    /**
     * 获取提现配置
     * @param ?string $name
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function config(?string $name = null)
    {
        static $data = [];
        if (empty($data)) $data = sysdata('TransferRule');
        return is_null($name) ? $data : ($data[$name] ?? '');
    }

    /**
     * 获取转账配置
     * @param ?string $name
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function payment(?string $name = null)
    {
        static $data = [];
        if (empty($data)) $data = sysdata('TransferWxpay');
        return is_null($name) ? $data : ($data[$name] ?? '');
    }

}