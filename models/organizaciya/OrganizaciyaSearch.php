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
    public $etapyObrazovaniyaSpisok;
    public $organizaciyaAdres;

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
            [['etapyObrazovaniyaSpisok'], 'safe'],
            [['organizaciyaAdres'], 'safe'],
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
        $query = Organizaciya::find()
            ->innerJoinWith(['vedomstvo0','adresAdresnyjObjekt']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'nazvanie',
                'organizaciyaAdres' => [
                    'asc' => ['adresnyj_objekt.oficialnoe_nazvanie' => SORT_ASC],
                    'desc' => ['adresnyj_objekt.oficialnoe_nazvanie' => SORT_DESC],
                ],
                'etapyObrazovaniyaSpisok' => [
                    'asc' => ['organizaciya.etapy_obrazovaniya' => SORT_ASC],
                    'desc' => ['organizaciya.etapy_obrazovaniya' => SORT_DESC],
                ],
                'obschij',
                'vedomstvoNazvanie' => [
                    'asc' => ['vedomstvo.nazvanie' => SORT_ASC],
                    'desc' => ['vedomstvo.nazvanie' => SORT_DESC],
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $filter='';
        if(!empty($this->etapyObrazovaniyaSpisok)) {
            $filter='{'.$this->etapyObrazovaniyaSpisok.'}';
        }

        $query->andFilterWhere(['like', 'organizaciya.nazvanie', $this->nazvanie])
            ->andFilterWhere(['organizaciya.obschij' => $this->obschij])
            ->andFilterWhere(['like', 'adresnyj_objekt.oficialnoe_nazvanie', $this->organizaciyaAdres])
            ->andFilterWhere(['&&', 'organizaciya.etapy_obrazovaniya', $filter])
            ->andFilterWhere(['like', 'vedomstvo.nazvanie', $this->vedomstvoNazvanie]);

        return $dataProvider;
    }
}
