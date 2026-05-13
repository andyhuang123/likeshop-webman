<?php
namespace app\adminapi\logic\marketing;

use app\common\logic\BaseLogic;
use app\common\model\marketing\BlindBox;
use app\common\model\marketing\BlindBoxDetail;
use app\common\service\FileService;
use think\facade\Db;

class BlindBoxLogic extends BaseLogic
{
    /**
     * @notes 添加盲盒
     * @param array $params
     * @return bool
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            if ($params['price'] < 0) {
                throw new \Exception('价格不能小于0');
            }

            // 1. 创建盲盒
            $blindBox = BlindBox::create([
                'name' => $params['name'],
                'price' => $params['price'],
                'image' => FileService::setFileUrl($params['image']),
                'status' => $params['status'],
                'sort' => $params['sort'] ?? 0,
            ]);

            // 2. 添加奖品
            if (!empty($params['blind_box_detail'])) {
                $details = [];
                foreach ($params['blind_box_detail'] as $item) {
                    if (isset($item['price']) && $item['price'] < 0) {
                        throw new \Exception('商品价格不能小于0');
                    }
                    if ($item['probability'] < 0) {
                        throw new \Exception('中奖概率不能小于0');
                    }
                    $details[] = [
                        'blind_box_id' => $blindBox->id,
                        'product_id' => $item['product_id'],
                        'probability' => $item['probability'],
                    ];
                }
                if (!empty($details)) {
                    (new BlindBoxDetail())->saveAll($details);
                }
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 编辑盲盒
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            if ($params['price'] < 0) {
                throw new \Exception('价格不能小于0');
            }

            // 1. 更新盲盒
            BlindBox::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'price' => $params['price'],
                'image' => FileService::setFileUrl($params['image']),
                'status' => $params['status'],
                'sort' => $params['sort'] ?? 0,
            ]);

            // 2. 更新奖品 (先删后加)
            BlindBoxDetail::where('blind_box_id', $params['id'])->delete();
            
            if (!empty($params['blind_box_detail'])) {
                $details = [];
                foreach ($params['blind_box_detail'] as $item) {
                    if (isset($item['price']) && $item['price'] < 0) {
                        throw new \Exception('商品价格不能小于0');
                    }
                    if ($item['probability'] < 0) {
                        throw new \Exception('中奖概率不能小于0');
                    }
                    $details[] = [
                        'blind_box_id' => $params['id'],
                        'product_id' => $item['product_id'],
                        'probability' => $item['probability'],
                    ];
                }
                if (!empty($details)) {
                    (new BlindBoxDetail())->saveAll($details);
                }
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除盲盒
     * @param array $params
     * @return bool
     */
    public static function delete(array $params): bool
    {
        // 软删除盲盒
        BlindBox::destroy($params['id']);
        // 也可以选择物理删除详情，或者保留
        // BlindBoxDetail::where('blind_box_id', $params['id'])->delete();
        return true;
    }

    /**
     * @notes 获取盲盒详情
     * @param array $params
     * @return array
     */
    public static function detail(array $params): array
    {
        $blindBox = BlindBox::findOrEmpty($params['id']);
        if ($blindBox->isEmpty()) {
            return [];
        }
        
        $blindBox = $blindBox->toArray();
        $blindBox['blind_box_detail'] = BlindBoxDetail::where('blind_box_id', $params['id'])
            ->with(['product' => function($query){
                $query->field('id,name,main_image,price');
            }])
            ->select()
            ->toArray();
            
        // 处理图片
        foreach ($blindBox['blind_box_detail'] as &$item) {
            if (isset($item['product']['main_image'])) {
                $item['product']['main_image'] = FileService::getFileUrl($item['product']['main_image']);
            }
        }

        return $blindBox;
    }
    
    /**
     * @notes 设置奖品
     * @param array $params
     * @return bool
     */
    public static function setPrizes(array $params): bool
    {
        // 校验参数
        $blindBoxId = $params['id'] ?? ($params['blind_box_id'] ?? 0);
        if (empty($blindBoxId)) {
            self::setError('参数缺失: id');
            return false;
        }
        if (!isset($params['blind_box_detail']) || !is_array($params['blind_box_detail'])) {
            self::setError('参数错误: blind_box_detail');
            return false;
        }

        Db::startTrans();
        try {
            // 检查盲盒是否存在
            $blindBox = BlindBox::findOrEmpty($blindBoxId);
            if ($blindBox->isEmpty()) {
                throw new \Exception('盲盒不存在');
            }

            // 验证并准备数据
            $details = [];
            foreach ($params['blind_box_detail'] as $item) {
                // 确保必要字段存在
                if (!isset($item['product_id']) || !isset($item['probability'])) {
                    throw new \Exception('奖品参数缺失');
                }
                
                // 校验概率非负
                if ($item['probability'] < 0) {
                    throw new \Exception('中奖概率不能小于0');
                }
                
                // 校验价格非负（如果有）
                if (isset($item['price']) && $item['price'] < 0) {
                     throw new \Exception('商品价格不能小于0');
                }
                
                $details[] = [
                    'blind_box_id' => $blindBoxId,
                    'product_id' => $item['product_id'],
                    'probability' => $item['probability'],
                ];
            }

            // 删除旧数据
            BlindBoxDetail::where('blind_box_id', $blindBoxId)->delete();

            // 插入新数据
            if (!empty($details)) {
                (new BlindBoxDetail())->saveAll($details);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改状态
     * @param array $params
     * @return bool
     */
    public static function status(array $params): bool
    {
        BlindBox::update([
            'id' => $params['id'],
            'status' => $params['status']
        ]);
        return true;
    }
}
