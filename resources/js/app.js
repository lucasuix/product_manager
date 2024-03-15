class User {

    constructor() {
        
        this.cadastrar = { request: "cadastro", data: {
                nome: '',
                quantidade: '',
                preco: '',
                descricao: ''
        } };
        this.deletar = { request: "delete", data: {
            id: ''
        } };
        this.atualizar = { request: "update", data: {
                id: '',
                nome: '',
                quantidade: '',
                preco: '',
                descricao: ''
        } };
        this.requirement = { request: "requirement", data: {
                quantidade: {value: false, order: "DESC"}, //Só vão ser adicionados quando o filtrar é true
                preco:      {value: false, order: "DESC"},
                buscar: '',
                offset: 0
        } };
        this.esta_cadastrando = false;
        this.esta_editando = false;
    }
        
        
        //Depois poderemos adicionar this.current_field para dizer em qual TIPO de campo o usuário está clicando
    
}



class BigBrother { //O grande irmão vigia o usuário e suas ações, verifica os dados no front-end

        constructor() {
            //fieldCadastro, fieldAtualizar, fieldDeletar, fieldFiltrar, fieldListar -> vou passar esses parâmetros, cada um deles vai ser uma parte do nosso documento para o BigBrother manipular
            
            this.campos = {
                nome: false,
                quantidade: false,
                preco: false
            };
            this.filtrar = false;
            this.listar = true;
            
            this.regex_id = /^[1-9]\d*$/;
            this.regex_nome = /^(?=.*[a-zA-Z]).{1,255}$/;
            this.regex_quantidade = /^(0|[1-9]\d*)$/;
            this.regex_preco = /^(?:0|[1-9]\d*)(?:\.\d+)?$/;
            this.regex_offset = /^(0|[1-9]\d*)$/;
            
        }
        
        
        verificarInformacoes(user) { 
            //User, é o usuário, e field é qual campo eu estou utilizando, fieldArray contém todos os inputs que são necessários ali, fieldArray["nome"], se refere ao campo nome no cadastro
        
            if (user.esta_cadastrando) return this.verificarDadosPorTipo(user.current_field, user.cadastrar.data);
            if (user.esta_editando) return this.verificarDadosPorTipo(user.current_field, user.atualizar.data);
            
            
        }
        
        setCamposTo(state) {
        
            this.campos.nome = state;
            this.campos.quantidade = state;
            this.campos.preco = state;
            
        }
        
        
        verificarDadosPorTipo(field_type, dados) {
            
            let result = {
                
                pass: true,
                reason: ""
                
            };
        
            switch (field_type) {
                
                    case "id":
                        
                        if(! this.regex_id.test(dados.id)) { 
                            result.pass = false;
                            result.reason = "ID deve conter apenas números inteiros positivos.";
                        }
                        break;
            
                    case "nome":
                        
                        if(! this.regex_nome.test(dados.nome)) {
                            result.pass = false;
                            result.reason = "Nome deve conter pelo menos uma letra e no máximo 255 caracteres.";
                        }
                        break;
                    
                    case "quantidade":
                    
                        if(! this.regex_quantidade.test(dados.quantidade)) {
                            result.pass = false;
                            result.reason = "Quantidade deve ser numérica, inteira, 0 ou maior que zero.";
                        }
                        break;

                    case "descricao":

                        result.pass = true;
                        break;
                    
                    case "preco":
                    
                        if(! this.regex_preco.test(dados.preco)) {
                            result.pass = false;
                            result.reason = "Preço deve ser numérico, maior que zero e positivo.";
                        }
                        break;
                    
                    case "offset":
                    
                        if(! this.regex_offset(dados.offset)) result.pass = false;
                        result.reason = "Range de acesso inválido";
                        break;
                        
                    default:
                        
                        result.pass = false;
                        break;
            }
            
            return result;
            
        }
    
    
}
