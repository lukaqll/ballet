<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted'             => 'O campo :attribute deve ser aceito.',
    'active_url'           => 'O campo :attribute não é uma URL válida.',
    'after'                => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal'       => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha'                => 'O campo :attribute só pode conter letras.',
    'alpha_dash'           => 'O campo :attribute só pode conter letras, números e traços.',
    'alpha_num'            => 'O campo :attribute só pode conter letras e números.',
    'array'                => 'O campo :attribute deve ser uma matriz.',
    'before'               => 'O campo :attribute deve ser uma data anterior a :date.',
    'before_or_equal'      => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
    'between'              => [
        'numeric' => 'O campo :attribute deve ser entre :min e :max.',
        'file'    => 'O campo :attribute deve ser entre :min e :max kilobytes.',
        'string'  => 'O campo :attribute deve ser entre :min e :max caracteres.',
        'array'   => 'O campo :attribute deve ter entre :min e :max itens.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'O campo :attribute de confirmação não confere.',
    'date'                 => 'O campo :attribute não é uma data válida.',
    'date_format'          => 'O campo :attribute não corresponde ao formato :format.',
    'different'            => 'Os campos :attribute e :other devem ser diferentes.',
    'digits'               => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between'       => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions'           => 'O campo :attribute tem dimensões de imagem inválidas.',
    'distinct'             => 'O campo :attribute campo tem um valor duplicado.',
    'email'                => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'exists'               => 'O campo :attribute selecionado é inválido.',
    'file'                 => 'O campo :attribute deve ser um arquivo.',
    'filled'               => 'O campo :attribute deve ter um valor.',
    'gt'                   => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file'    => 'O campo :attribute deve ser maior que :value kilobytes.',
        'string'  => 'O campo :attribute deve ser maior que :value caracteres.',
        'array'   => 'O campo :attribute deve conter mais de :value itens.',
    ],
    'gte'                  => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'file'    => 'O campo :attribute deve ser maior ou igual a :value kilobytes.',
        'string'  => 'O campo :attribute deve ser maior ou igual a :value caracteres.',
        'array'   => 'O campo :attribute deve conter :value itens ou mais.',
    ],
    'image'                => 'O campo :attribute deve ser uma imagem.',
    'in'                   => 'O campo :attribute selecionado é inválido.',
    'in_array'             => 'O campo :attribute não existe em :other.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'ip'                   => 'O campo :attribute deve ser um endereço de IP válido.',
    'ipv4'                 => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6'                 => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json'                 => 'O campo :attribute deve ser uma string JSON válida.',
    'lt'                   => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'file'    => 'O campo :attribute deve ser menor que :value kilobytes.',
        'string'  => 'O campo :attribute deve ser menor que :value caracteres.',
        'array'   => 'O campo :attribute deve conter menos de :value itens.',
    ],
    'lte'                  => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'file'    => 'O campo :attribute deve ser menor ou igual a :value kilobytes.',
        'string'  => 'O campo :attribute deve ser menor ou igual a :value caracteres.',
        'array'   => 'O campo :attribute não deve conter mais que :value itens.',
    ],
    'max'                  => [
        'numeric' => 'O campo :attribute não pode ser superior a :max.',
        'file'    => 'O campo :attribute não pode ser superior a :max kilobytes.',
        'string'  => 'O campo :attribute não pode ser superior a :max caracteres.',
        'array'   => 'O campo :attribute não pode ter mais do que :max itens.',
    ],
    'mimes'                => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes'            => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min'                  => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'file'    => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string'  => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array'   => 'O campo :attribute deve ter pelo menos :min itens.',
    ],
    'not_in'               => 'O campo :attribute selecionado é inválido.',
    'not_regex'            => 'O campo :attribute possui um formato inválido.',
    'numeric'              => 'O campo :attribute deve ser um número.',
    'present'              => 'O campo :attribute deve estar presente.',
    'regex'                => 'O campo :attribute tem um formato inválido.',
    'required'             => 'O campo :attribute é obrigatório.',
    'required_if'          => 'O campo :attribute é obrigatório quando :other for :value.',
    'required_unless'      => 'O campo :attribute é obrigatório exceto quando :other for :values.',
    'required_with'        => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all'    => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_without'     => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values estão presentes.',
    'same'                 => 'Os campos :attribute e :other devem corresponder.',
    'size'                 => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'file'    => 'O campo :attribute deve ser :size kilobytes.',
        'string'  => 'O campo :attribute deve ser :size caracteres.',
        'array'   => 'O campo :attribute deve conter :size itens.',
    ],
    'string'               => 'O campo :attribute deve ser uma string.',
    'timezone'             => 'O campo :attribute deve ser uma zona válida.',
    'unique'               => 'Este :attribute já está sendo utilizado.',
    'uploaded'             => 'Ocorreu uma falha no upload do campo :attribute.',
    'url'                  => 'O campo :attribute tem um formato inválido.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'attribute-name' => [
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
        'amount'                    => 'Quantidade',
        'email'                     => 'E-mail',
        'name'                      => 'Nome',
        'id_sector'                 => 'Setor',
        'id_size'                   => 'Tamanho',
        'id_store'                  => 'Loja',
        'id_category'               => 'Categoria',
        'id_product'                => 'Produto',
        'password'                  => 'Senha',
        'password_confirmation'     => 'Confirmação de Senha',
        'phone'                     => 'Telefone',
        'cnpj'                      => 'CNPJ',
        'cpf'                       => 'CPF',
        'doc_number'                => 'CPF',
        'id_general_payment_menthod'=> 'Forma de Pagamento',
        'id_general_categort'       => 'Categoria',
        'id_city'                   => 'Cidade',
        'id_district'               => 'Bairro',
        'street'                    => 'Rua',
        'id_uf'                     => 'UF',
        'city'                      => 'Cidade',
        'district'                  => 'Bairro',
        'initials'                  => 'Sigla',
        'complement'                => 'Complemento',
        'reference'                 => 'Referência',
        'new_password'              => 'Nova Senha',
        'confirm_new_password'      => 'Confirme a Nova Senha',
        'id_general_category'       => 'Categoria Geral',
        'price'                     => 'Valor',
        'id_product_size'           => 'Tamanho',
        'image'                     => 'Imagem',
        'number'                    => 'Número',
        'store_name'                => 'Nome',
        'info'                      => 'Informações',
        'contact'                   => 'Contato',
        'reply'                     => 'Resposta',
        'rate'                      => 'Avaliação',
        'rate.product'              => 'Avaliação do produto',
        'rate.delivery'             => 'Avaliação da entrega',
        'rate.general'              => 'Avaliação geral',
        'amount'                    => 'Quantidade',
        'birthdate'                 => 'Data de Nascimento',
        'today'                     => 'hoje',
        'operation'                 => 'Operação',
        'type'                      => 'Tipo',
        'attribute'                 => 'Atributo',
        'value'                     => 'Valor',
        'freight'                   => 'Frete',
        'start'                     => 'Hora de Abertura',
        'end'                       => 'Hora de Fechamento',
        'notes'                     => 'Observação',
        
        'id_user'                   => 'Usuário',
        'nick_name'                 => 'Apelido',

        'user_name'     => 'Nome do Usuário',
        'user_email'    => 'E-mail do Usuário',
        'user_cpf'      => 'CPF do Usuário',
        'user_phone'    => 'Telefone do Usuário',
        'user_uf'       => 'UF do Usuário',
        'user_city'     => 'Cidade do Usuário',
        'user_district' => 'Bairro do Usuário',
        'user_street'   => 'Cidade do Usuário',
        'user_rg'       => 'RG do Usuário',
        'user_orgao_exp' => 'Órgão do Usuário',
        'user_birthdate' => 'Aniversário do Usuário',
        'user_cep'       => 'CEP do Usuário',
        'user_instagram' => 'Instagram do Usuário',
        'user_know_by'   => 'Como Nos Conheceu',

        'student_name'     => 'Nome do Aluno',
        'student_nick_name' => 'Apelido do Aluno',
        'student_birthdate' => 'Aniversário do Aluno',
        'student_id_class'  => 'Aula',
        
        'file_doc1' => 'Arquivo 1',
        'file_doc2' => 'Arquivo 2',
        'file_doc3' => 'Arquivo 3',
    ],
];