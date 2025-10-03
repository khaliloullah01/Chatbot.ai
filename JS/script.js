// Configuration
const CLE_API = "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"; 
const URL_API = "https://openrouter.ai/api/v1/chat/completions";
const MODELE_FIXE = "meta-llama/llama-3.3-70b-instruct:free";

let enAttente = false;
let controleurRequete = null; // Pour l'abort de la requête

const saisieUtilisateur = document.getElementById('saisie-utilisateur');
const btnEnvoyer = document.getElementById('btn-envoyer');
const btnArreter = document.getElementById('btn-arreter');
const boiteChat = document.getElementById('boite-chat');
const btnNouvelleConversation = document.getElementById('btn-nouvelle-conversation');
const btnMenuMobile = document.getElementById('btn-menu-mobile');
const barreLaterale = document.getElementById('barre-laterale');

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Focalisation sur la saisie au chargement
    saisieUtilisateur.focus();
    
    // Événements
    btnEnvoyer.addEventListener('click', envoyerMessage);
    btnArreter.addEventListener('click', arreterReponse);
    saisieUtilisateur.addEventListener('keypress', gererToucheEntree);
    btnNouvelleConversation.addEventListener('click', commencerNouvelleConversation);
    btnMenuMobile.addEventListener('click', toggleMenuMobile);
    
    // Message de bienvenue initial
    setTimeout(() => {
        ajouterMessage("Bonjour ! Je suis tdsi.ai, votre assistant pédagogique. Je peux vous aider en mathématiques, informatique et sciences. Posez-moi vos questions !", 'bot');
    }, 500);

    // Initialisation de la modal (cacher au chargement)
    const modal = document.getElementById('confirmation-modal');
    if (modal) {
        modal.style.display = 'none';
    }
});

// Fonction pour envoyer un message
async function envoyerMessage() {
    if (enAttente) return;
    
    const message = saisieUtilisateur.value.trim();
    if (message === '') return;
    
    // Afficher le message de l'utilisateur
    ajouterMessage(message, 'utilisateur');
    saisieUtilisateur.value = '';
    btnEnvoyer.disabled = true;
    enAttente = true;
    btnArreter.style.display = 'inline-block';
    
    // Afficher l'indicateur de frappe
    const elementChargement = ajouterIndicateurFrappe();
    
    try {
        // Créer un nouveau contrôleur pour pouvoir annuler la requête
        controleurRequete = new AbortController();
        const signal = controleurRequete.signal;
        
        const reponse = await fetch(URL_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${CLE_API}`
            },
            body: JSON.stringify({
                model: MODELE_FIXE,
                messages: [
                    { 
                        role: "system",
                        content: `Tu es tdsi.ai, assistant pédagogique universitaire expert en mathématiques et informatique.
                            MISSION :
                            - Expliquer les concepts par étapes progressives
                            - Fournir des exemples concrets et des exercices
                            - Adapter le niveau à l'étudiant (LICENCE/MASTER)
                            - Corriger les erreurs avec bienveillance
                            - Proposer des analogies pédagogiques

                            DOMAINES COUVERTS :
                            Mathématiques : Algèbre, Analyse, Probabilités, Statistiques, Calcul différentiel
                            Informatique : Algorithmique, Programmation, Bases de données, IA, Web
                            Sciences : Mathématiques appliquées, Calcul scientifique, Physique mathématique, Cryptographie

                            TON :
                            - Pédagogique mais précis
                            - Encourageant et patient
                            - Structuré avec des étapes claires
                            - Exemples concrets et exercices pratiques
                            - Réponses complètes mais concises

                            FORMAT DE RÉPONSE :
                            1. Compréhension du problème
                            2. Concepts théoriques
                            3. Résolution étape par étape
                            4. Exemple concret
                            5. Application pratique

                            Pour les blocs de code, utilise le formatage markdown avec le nom du langage.`
                    },
                    { role: 'user', content: message }
                ],
                temperature: 0.7,
                max_tokens: 10000
            }),
            signal: signal
        });
        
        // Vérifier le statut HTTP
        if (!reponse.ok) {
            const errorText = await reponse.text();
            throw new Error(`Erreur HTTP ${reponse.status}: ${errorText}`);
        }
        
        const donnees = await reponse.json();
        
        // Debug
        console.log('Réponse API:', donnees);
        
        // Gestion d'erreur améliorée
        if (!donnees) {
            throw new Error('Aucune réponse de l\'API');
        }
        
        if (donnees.error) {
            throw new Error(`Erreur API: ${donnees.error.message || 'Erreur inconnue'}`);
        }
        
        if (!donnees.choices || !Array.isArray(donnees.choices) || donnees.choices.length === 0) {
            throw new Error('Structure de réponse invalide - choix manquants');
        }
        
        const premierChoix = donnees.choices[0];
        if (!premierChoix.message || !premierChoix.message.content) {
            throw new Error('Structure de réponse invalide - contenu du message manquant');
        }
        
        let reponseBot = premierChoix.message.content;
        
        // Convertir le markdown en HTML pour une meilleure présentation
        reponseBot = convertirMarkdownEnHTML(reponseBot);
        
        // Remplacer l'indicateur de frappe par la réponse
        boiteChat.removeChild(elementChargement);
        const messageElement = ajouterMessageHTML(reponseBot, 'bot');
        
        // Ajouter le bouton de copie
        ajouterBoutonCopie(messageElement, reponseBot);
        
        // Ajouter une indication du modèle utilisé
        const horodatage = messageElement.querySelector('.horodatage');
        if (horodatage) {
            horodatage.textContent += ' • tdsi.ai ';
        }
        
        // Re-rendre MathJax pour les formules mathématiques
        if (window.MathJax) {
            MathJax.typesetPromise();
        }
        
    } catch (error) {
        if (error.name === 'AbortError') {
            console.log("Requête annulée par l'utilisateur");
            boiteChat.removeChild(elementChargement);
            ajouterMessage("Réponse interrompue.", 'bot');
        } else {
            console.error("Erreur lors de l'envoi du message:", error);
            boiteChat.removeChild(elementChargement);
            ajouterMessage("Désolé, une erreur s'est produite. Veuillez réessayer dans un moment.", 'erreur');
        }
    } finally {
        enAttente = false;
        btnEnvoyer.disabled = false;
        btnArreter.style.display = 'none';
        controleurRequete = null;
        saisieUtilisateur.focus();
    }
}

// Fonction pour arrêter la réponse en cours
function arreterReponse() {
    if (controleurRequete && enAttente) {
        controleurRequete.abort();
        enAttente = false;
        btnEnvoyer.disabled = false;
        btnArreter.style.display = 'none';
    }
}

// Fonction pour ajouter un bouton de copie au message
function ajouterBoutonCopie(messageElement, contenu) {
    const boutonCopie = document.createElement('button');
    boutonCopie.classList.add('btn-copier');
    boutonCopie.title = 'Copier le texte';
    boutonCopie.innerHTML = '<i class="far fa-copy"></i>';
    
    boutonCopie.addEventListener('click', function() {
        // Créer un élément textarea temporaire pour la copie
        const textarea = document.createElement('textarea');
        textarea.value = extraireTexte(contenu);
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        // Animation de confirmation
        boutonCopie.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            boutonCopie.innerHTML = '<i class="far fa-copy"></i>';
        }, 2000);
    });
    
    // Ajouter le bouton au message
    const contenuMessage = messageElement.querySelector('.contenu-message');
    contenuMessage.style.position = 'relative';
    boutonCopie.style.position = 'absolute';
    boutonCopie.style.top = '5px';
    boutonCopie.style.right = '5px';
    boutonCopie.style.background = 'rgba(255, 255, 255, 0.7)';
    boutonCopie.style.border = 'none';
    boutonCopie.style.borderRadius = '3px';
    boutonCopie.style.padding = '3px 5px';
    boutonCopie.style.cursor = 'pointer';
    boutonCopie.style.fontSize = '12px';
    
    contenuMessage.appendChild(boutonCopie);
}

// Fonction pour extraire le texte d'un contenu HTML
function extraireTexte(html) {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
}

// Fonction pour convertir le markdown en HTML (version améliorée)
function convertirMarkdownEnHTML(texte) {
    if (!texte) return '';
    // Protéger les formules LaTeX pendant le traitement
    const protectedFormulas = [];
    texte = texte.replace(/\\\[(.*?)\\\]|\\\((.*?)\\\)/g, match => {
        protectedFormulas.push(match);
        return `@@FORMULA${protectedFormulas.length - 1}@@`;
    });
    // Convertir les blocs de code
    texte = texte.replace(/```(\w+)?\s*([\s\S]*?)```/g, (match, lang, code) => {
        lang = lang || 'text';
        return `<div class="code-block"><div class="code-header">${lang.toUpperCase()}</div><pre><code class="language-${lang}">${escapeHtml(code.trim())}</code></pre></div>`;
    });
    // Convertir le code inline
    texte = texte.replace(/`([^`]+)`/g, '<code class="inline-code">$1</code>');
    // Convertir les sauts de ligne
    texte = texte.replace(/\n/g, '<br>');
    // Convertir le gras (**texte**)
    texte = texte.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    // Convertir l'italique (*texte*)
    texte = texte.replace(/\*(.*?)\*/g, '<em>$1</em>');
    // Convertir les titres
    texte = texte.replace(/### (.*?)(<br>|$)/g, '<h3>$1</h3>');
    texte = texte.replace(/## (.*?)(<br>|$)/g, '<h2>$1</h2>');
    texte = texte.replace(/# (.*?)(<br>|$)/g, '<h1>$1</h1>');
    // Convertir les listes à puces
    texte = texte.replace(/^- (.*?)(<br>|$)/g, '<li>$1</li>');
    texte = texte.replace(/(<li>.*?<\/li>)(?=\s*[^<]|$)/gs, '<ul>$1</ul>');
    // Convertir les listes numérotées
    texte = texte.replace(/^(\d+)\. (.*?)(<br>|$)/g, '<li>$2</li>');
    texte = texte.replace(/(<li>.*?<\/li>)(?=\s*[^<]|$)/gs, '<ol>$1</ol>');
    // Convertir les citations (> texte)
    texte = texte.replace(/^> (.*?)(<br>|$)/g, '<blockquote>$1</blockquote>');
    // Restaurer les formules LaTeX
    texte = texte.replace(/@@FORMULA(\d+)@@/g, (match, index) => {
        return protectedFormulas[parseInt(index)];
    });
    // Nettoyer les balises mal formées
    texte = texte.replace(/<ul><br>/g, '<ul>');
    texte = texte.replace(/<ol><br>/g, '<ol>');
    texte = texte.replace(/<\/ul><br>/g, '</ul>');
    texte = texte.replace(/<\/ol><br>/g, '</ol>');
    return texte;
}

// Fonction pour échapper le HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Fonction pour ajouter un message formaté en HTML
function ajouterMessageHTML(html, type) {
    const elementMessage = document.createElement('div');
    elementMessage.classList.add('message', type);
    
    const contenuMessage = document.createElement('div');
    contenuMessage.classList.add('contenu-message');
    contenuMessage.innerHTML = html;
    elementMessage.appendChild(contenuMessage);
    
    const horodatage = document.createElement('div');
    horodatage.classList.add('horodatage');
    horodatage.textContent = new Date().toLocaleTimeString();
    elementMessage.appendChild(horodatage);
    
    boiteChat.appendChild(elementMessage);
    boiteChat.scrollTop = boiteChat.scrollHeight;
    
    return elementMessage;
}

// Fonction pour ajouter un message texte simple
function ajouterMessage(texte, type) {
    const elementMessage = document.createElement('div');
    elementMessage.classList.add('message', type);
    
    const contenuMessage = document.createElement('div');
    contenuMessage.classList.add('contenu-message');
    contenuMessage.textContent = texte;
    elementMessage.appendChild(contenuMessage);
    
    const horodatage = document.createElement('div');
    horodatage.classList.add('horodatage');
    horodatage.textContent = new Date().toLocaleTimeString();
    elementMessage.appendChild(horodatage);
    
    boiteChat.appendChild(elementMessage);
    boiteChat.scrollTop = boiteChat.scrollHeight;
    
    return elementMessage;
}

// Fonction pour ajouter un indicateur de frappe
function ajouterIndicateurFrappe() {
    const indicateur = document.createElement('div');
    indicateur.classList.add('message', 'bot');
    indicateur.id = 'indicateur-frappe';
    
    const frappe = document.createElement('div');
    frappe.classList.add('indicateur-frappe');
    frappe.innerHTML = `
        <div class="frappe-container">
            <i class="fas fa-graduation-cap"></i>
            <div class="points">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    `;
    
    indicateur.appendChild(frappe);
    boiteChat.appendChild(indicateur);
    boiteChat.scrollTop = boiteChat.scrollHeight;
    
    return indicateur;
}

// Gestion de la touche Entrée
function gererToucheEntree(event) {
    if (event.key === 'Enter' && !event.shiftKey && !enAttente) {
        event.preventDefault();
        envoyerMessage();
    }
}

// Nouvelle conversation
function commencerNouvelleConversation() {
  // Afficher la modal personnalisée
  const modal = document.getElementById('confirmation-modal');
  modal.style.display = 'flex';
  
  // Gestionnaire pour le bouton OK
  document.getElementById('confirm-ok').onclick = function() {
    boiteChat.innerHTML = '';
    ajouterMessage("Conversation réinitialisée. Bonjour ! Je suis tdsi.ai, votre assistant pédagogique. Comment puis-je vous aider aujourd'hui ?", 'bot');
    modal.style.display = 'none';
  };
  
  // Gestionnaire pour le bouton Annuler
  document.getElementById('confirm-cancel').onclick = function() {
    modal.style.display = 'none';
  };
  
  // Gestionnaire pour la croix de fermeture
  document.querySelector('.close-modal').onclick = function() {
    modal.style.display = 'none';
  };
  
  // Fermer la modal en cliquant à l'extérieur
  modal.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  };
}

// Toggle menu mobile
function toggleMenuMobile() {
  barreLaterale.classList.toggle('active');
}

// Gestionnaire d'erreurs global
window.addEventListener('error', function(e) {
    console.error('Erreur globale:', e.error);
    if (!enAttente) {
        ajouterMessage("Une erreur inattendue s'est produite. Veuillez recharger la page.", 'erreur');
    }
});

// Fonction utilitaire pour le défilement automatique
function defilerVersBas() {
    boiteChat.scrollTop = boiteChat.scrollHeight;
}

// Observer les nouveaux messages pour le défilement automatique
const observer = new MutationObserver(defilerVersBas);
observer.observe(boiteChat, { childList: true, subtree: true });

// Fonction pour redimensionner automatiquement la zone de texte
function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';
}

// Initialiser la hauteur de la zone de texte
saisieUtilisateur.addEventListener('input', function() {
    autoResize(this);
});