/**
 * Arquivo de inicialização do JavaScript
 * 
 * Este arquivo configura bibliotecas e configurações globais
 * que serão usadas em toda a aplicação JavaScript.
 */

// Importa e configura o Axios para requisições HTTP
import axios from 'axios';
window.axios = axios;

// Define o header padrão para identificar requisições AJAX
// Isso ajuda o Laravel a identificar requisições que vêm do JavaScript
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
