<?php

namespace app\models\organizaciya;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\organizaciya\Organizaciya;

/**
 * OrganizaciyaSearch represents the model behind the search form about `app\models\organizaciya\Organizaciya`.
 */
class OrganizaciyaSearch extends Organizaciya
{
    public $vedomstvoNazvanie;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'adres_adresnyj_objekt', 'vedomstvo'], 'integer'],
            [['nazvanie', 'adres_dom', 'etapy_obrazovaniya'], 'safe'],
            [['obschij'], 'boolean'],
            [['vedomstvoNazvanie'], 'safe'],
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
        $query = Organizaciya::find();
        $query->innerJoinWith(['vedomstvo0']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'nazvanie',
                'adres_dom',
                'etapy_obrazovaniya',
                'obschij',
                'vedomstvoNazvanie' => [
                    'asc' => ['vedomstvo.nazvanie' => SORT_ASC],
                    'desc' => ['vedomstvo.nazvanie' => SORT_DESC],
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            //'id' => $this->id,
            'adres_adresnyj_objekt' => $this->adres_adresnyj_objekt,
            'obschij' => $this->obschij,
            //'vedomstvo' => $this->vedomstvo,
        ]);

        $query->andFilterWhere(['like', 'nazvanie', $this->nazvanie])
            ->andFilterWhere(['like', 'adres_dom', $this->adres_dom])
            ->andFilterWhere(['like', 'etapy_obrazovaniya', $this->etapy_obrazovaniya])
            ->andFilterWhere(['like', 'vedomstvo.nazvanie', $this->vedomstvoNazvanie]);

        return $dataProvider;
    }
}
