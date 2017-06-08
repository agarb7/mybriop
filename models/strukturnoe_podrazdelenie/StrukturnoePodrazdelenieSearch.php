<?php

namespace app\models\strukturnoe_podrazdelenie;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie;

/**
 * StrukturnoePodrazdelenieSearch represents the model behind the search form about `app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie`.
 */
class StrukturnoePodrazdelenieSearch extends StrukturnoePodrazdelenie
{
    public $organizaciyaNazvanie;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organizaciya'], 'integer'],
            [['nazvanie', 'sokrashennoe_nazvanie'], 'string'],
            [['obschij'], 'boolean'],
            [['organizaciyaNazvanie'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = StrukturnoePodrazdelenie::find();
        $query->joinWith(['organizaciya0']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'nazvanie',
                'sokrashennoe_nazvanie',
                'obschij',
                'organizaciyaNazvanie' => [
                    'asc' => ['organizaciya.nazvanie' => SORT_ASC],
                    'desc' => ['organizaciya.nazvanie' => SORT_DESC],
                    'label' => 'Организация',
                ]
            ]
        ]);
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'strukturnoe_podrazdelenie.nazvanie', $this->nazvanie])
            ->andFilterWhere(['like', 'strukturnoe_podrazdelenie.sokrashennoe_nazvanie', $this->sokrashennoe_nazvanie])
            ->andFilterWhere(['like', 'organizaciya.nazvanie', $this->organizaciyaNazvanie])
            ->andFilterWhere(['strukturnoe_podrazdelenie.obschij' => $this->obschij]);

        return $dataProvider;
    }
}
