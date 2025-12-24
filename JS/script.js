// Configuration
const CLE_API = "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";
const URL_API = "https://openrouter.ai/api/v1/chat/completions";
const MODELE_FIXE = "meta-llama/llama-3.3-70b-instruct:free";

// État de l'application
let enAttente = false;
let controleurRequete = null;
let historiqueConversation = [];

// Préférences utilisateur
const preferencesUtilisateur = {
    prenom: '',
    nom: '',
    niveau: '',
    email: '',
    autoScroll: true,
    sons: true,
    vitesseFrappe: 'normal'
};

// Éléments DOM
const saisieUtilisateur = document.getElementById('saisie-utilisateur');
const btnEnvoyer = document.getElementById('btn-envoyer');
const btnArreter = document.getElementById('btn-arreter');
const boiteChat = document.getElementById('boite-chat');

// ===== INITIALISATION =====
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM chargé - initialisation du chatbot TDSI');

    if (!saisieUtilisateur || !btnEnvoyer || !boiteChat) {
        console.error('Éléments DOM manquants');
        return;
    }

    initialiserEvenements();
    chargerPreferences();
    afficherMessageBienvenue();
});

// ===== GESTION DES ÉVÉNEMENTS =====
function initialiserEvenements() {
    btnEnvoyer.addEventListener('click', envoyerMessage);

    if (btnArreter) {
        btnArreter.addEventListener('click', arreterReponse);
    }

    saisieUtilisateur.addEventListener('keypress', function (event) {
        if (event.key === 'Enter' && !event.shiftKey && !enAttente) {
            event.preventDefault();
            envoyerMessage();
        }
    });

    saisieUtilisateur.addEventListener('input', function () {
        gererBoutonsFrappe();
        autoResize(this);
    });

    gererBoutonsFrappe();
    saisieUtilisateur.focus();
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

// ===== GESTION DES PRÉFÉRENCES =====
function chargerPreferences() {
    const userData = document.getElementById('user-data');
    if (userData) {
        preferencesUtilisateur.prenom = userData.dataset.prenom || 'Étudiant';
        preferencesUtilisateur.nom = userData.dataset.nom || '';
        preferencesUtilisateur.niveau = userData.dataset.niveau || 'TDSI';
        preferencesUtilisateur.email = userData.dataset.email || '';
    } else {
        preferencesUtilisateur.prenom = 'Étudiant';
        preferencesUtilisateur.niveau = 'TDSI';
    }

    // Configuration des écouteurs pour les paramètres
    const autoScroll = document.getElementById('auto-scroll');
    const sons = document.getElementById('sons');
    const vitesseFrappe = document.getElementById('vitesse-frappe');

    if (autoScroll) {
        autoScroll.checked = preferencesUtilisateur.autoScroll;
        autoScroll.addEventListener('change', function () {
            preferencesUtilisateur.autoScroll = this.checked;
        });
    }

    if (sons) {
        sons.checked = preferencesUtilisateur.sons;
        sons.addEventListener('change', function () {
            preferencesUtilisateur.sons = this.checked;
        });
    }

    if (vitesseFrappe) {
        vitesseFrappe.value = preferencesUtilisateur.vitesseFrappe;
        vitesseFrappe.addEventListener('change', function () {
            preferencesUtilisateur.vitesseFrappe = this.value;
        });
    }
}

// ===== FONCTIONS D'AFFICHAGE =====
function afficherMessageBienvenue() {
    setTimeout(() => {
        const messageBienvenue = `Salut ${preferencesUtilisateur.prenom} ${preferencesUtilisateur.nom} ! Comment puis-je t'aider aujourd'hui ?`;
        ajouterMessage(messageBienvenue, 'bot');
    }, 1000);
}

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
    defilerVersBas();

    return elementMessage;
}

function creerElementReponseProgressive() {
    const elementMessage = document.createElement('div');
    elementMessage.classList.add('message', 'bot', 'reponse-progressive');

    const contenuMessage = document.createElement('div');
    contenuMessage.classList.add('contenu-message');
    contenuMessage.innerHTML = '<div class="curseur-frappe">|</div>';
    elementMessage.appendChild(contenuMessage);

    const horodatage = document.createElement('div');
    horodatage.classList.add('horodatage');
    horodatage.textContent = new Date().toLocaleTimeString();
    elementMessage.appendChild(horodatage);

    boiteChat.appendChild(elementMessage);
    defilerVersBas();

    return elementMessage;
}

// ===== FONCTION PRINCIPALE D'ENVOI =====
async function envoyerMessage() {
    if (enAttente) return;

    const message = saisieUtilisateur.value.trim();
    if (message === '') return;

    console.log('Envoi du message utilisateur:', message);

    // Cacher les suggestions après le premier message
    cacherSuggestionsAvecAnimation();

    // Sauvegarder le message utilisateur
    await sauvegarderMessage(message, 'utilisateur');

    ajouterMessage(message, 'utilisateur');
    historiqueConversation.push({ role: 'user', content: message });
    reinitialiserInterfaceEnvoi();

    try {
        controleurRequete = new AbortController();
        const elementReponse = creerElementReponseProgressive();

        const reponse = await fetch(URL_API, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${CLE_API}`,
                'HTTP-Referer': window.location.origin,
                'X-Title': 'TDSI ChatBot Assistant'
            },
            body: JSON.stringify({
                model: MODELE_FIXE,
                messages: preparerMessages(),
                temperature: 0.7,
                max_tokens: 2000,
                stream: true
            }),
            signal: controleurRequete.signal
        });

        if (!reponse.ok) {
            const errorText = await reponse.text();
            throw new Error(`Erreur HTTP ${reponse.status}: ${errorText}`);
        }

        const reponseComplete = await traiterReponseStream(reponse, elementReponse);

        // Sauvegarder la réponse du bot
        await sauvegarderMessage(reponseComplete, 'bot');

        finaliserReponse(elementReponse, reponseComplete);

    } catch (error) {
        gererErreurEnvoi(error);
    } finally {
        finaliserEnvoi();
    }
}
// ===== FONCTION DE SAUVEGARDE =====
async function sauvegarderMessage(contenu, type) {
    try {
        const response = await fetch('includes/sauvegarder_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contenu: contenu,
                type: type
            })
        });

        const result = await response.json();

        if (!result.success) {
            console.error('Erreur sauvegarde:', result.error);
        }

        return result;
    } catch (error) {
        console.error('Erreur réseau sauvegarde:', error);
    }
}

function preparerMessages() {
    return [
        {
            role: "system",
            content: `Tu es tdsi.ai, assistant pédagogique universitaire créé par Ibrahima Sylla un étudiant de la licence 2 de l'école TDSI de l'Université Cheikh Anta Diop de Dakar.
                        Tu est expert en Transmission de Données et Sécurité de l'Information et en mathematiques appliquer a la cryptogrophie.

                INFORMATIONS UTILISATEUR:
                - Prénom: ${preferencesUtilisateur.prenom}
                - Niveau: ${preferencesUtilisateur.niveau}
                - Domaine: TDSI (Transmission de Données et Sécurité de l'Information), Mathématiques appliquées à la cryptographie et à la sécurité des réseaux.

                MISSION :
                - Adapter tes explications au niveau "${preferencesUtilisateur.niveau}"
                - Expliquer les concepts TDSI par étapes progressives
                - Fournir des exemples concrets et des exercices
                - Corriger les erreurs avec bienveillance
                - Proposer des analogies pédagogiques

                DOMAINES COUVERTS :
                - Cryptographie : Algorithmes, protocoles, chiffrement
                - Sécurité réseau : Firewalls, VPN, détection d'intrusion
                - Transmission de données : Protocoles, routage, QoS
                - Algèbre pour la cryptographie : Théorie des groupes, arithmétique modulaire
                - Programmation sécurisée : Bonnes pratiques, vulnérabilités

                TON :
                - Pédagogique mais précis
                - Encourageant et patient
                - Structuré avec des étapes claires
                - Réponses adaptées au niveau "${preferencesUtilisateur.niveau}"
                - Exemples concrets et exercices pratiques

                FORMAT DE RÉPONSE :
                - Compréhension du problème
                - Concepts théoriques adaptés au niveau
                - Résolution étape par étape
                - Exemple concret en TDSI
                - Application pratique
                - Résumé des points clés`
        },
        ...historiqueConversation.slice(-8)
    ];
}

// ===== GESTION DU STREAMING =====
async function traiterReponseStream(reponse, elementReponse) {
    const reader = reponse.body.getReader();
    const decoder = new TextDecoder();
    const contenuMessage = elementReponse.querySelector('.contenu-message');

    let reponseComplete = '';
    let buffer = '';
    let dernierRafraichissement = Date.now();
    const delaiRafraichissement = obtenirDelaiRafraichissement();

    contenuMessage.innerHTML = '';

    try {
        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lignes = buffer.split('\n');
            buffer = lignes.pop();

            for (const ligne of lignes) {
                const ligneTrim = ligne.trim();
                if (ligneTrim.startsWith('data: ') && ligneTrim !== 'data: [DONE]') {
                    const token = traiterLigneStream(ligneTrim);
                    if (token) {
                        reponseComplete += token;

                        const maintenant = Date.now();
                        if (maintenant - dernierRafraichissement >= delaiRafraichissement) {
                            mettreAJourAffichage(contenuMessage, reponseComplete);
                            dernierRafraichissement = maintenant;
                            defilerVersBas();
                        }
                    }
                }
            }
        }

        mettreAJourAffichage(contenuMessage, reponseComplete);
        defilerVersBas();
        return reponseComplete;

    } finally {
        reader.releaseLock();
    }
}

function obtenirDelaiRafraichissement() {
    switch (preferencesUtilisateur.vitesseFrappe) {
        case 'rapide': return 20;
        case 'lent': return 100;
        default: return 50;
    }
}

function traiterLigneStream(ligne) {
    try {
        const donnees = JSON.parse(ligne.substring(6));
        if (donnees.choices?.[0]?.delta?.content) {
            return donnees.choices[0].delta.content;
        }
    } catch (e) {
        console.log('Ligne JSON invalide ignorée:', ligne);
    }
    return null;
}

function mettreAJourAffichage(contenuMessage, texte) {
    contenuMessage.innerHTML = convertirMarkdownEnHTML(texte) + '<div class="curseur-frappe">|</div>';

    // Appliquer la coloration syntaxique après l'insertion (meilleure version)
    setTimeout(() => {
        contenuMessage.querySelectorAll('.code-terminal').forEach((terminal) => {
            // Si la coloration a déjà été appliquée, skip
            if (terminal.dataset.colored === '1') return;

            // Pour chaque ligne on fait highlightAuto sur son texte brut
            terminal.querySelectorAll('.line-content').forEach((lineEl) => {
                const text = lineEl.textContent || lineEl.innerText || '';
                if (!text.trim()) return;

                // highlight.js auto-detecte la langue et renvoie du HTML
                try {
                    const result = hljs.highlightAuto(text);
                    // result.value contient le HTML mis en évidence
                    lineEl.innerHTML = result.value;
                } catch (err) {
                    // fallback: on laisse le texte brut (déjà échappé)
                    lineEl.textContent = text;
                }
            });

            // Marquer comme coloré pour éviter re-coloration inutile
            terminal.dataset.colored = '1';
        });

        // Aussi appliquer highlight auto sur <pre><code> si tu en as ailleurs
        document.querySelectorAll('pre code').forEach((block) => {
            if (!block.dataset.hljs) {
                try {
                    hljs.highlightElement(block);
                } catch (e) { }
                block.dataset.hljs = '1';
            }
        });

    }, 80);

}

// ===== FONCTIONS DE FINALISATION =====
function finaliserReponse(elementReponse, reponseComplete) {
    const contenuMessage = elementReponse.querySelector('.contenu-message');
    contenuMessage.innerHTML = convertirMarkdownEnHTML(reponseComplete);

    historiqueConversation.push({ role: 'assistant', content: reponseComplete });
    ajouterBoutonCopie(elementReponse, reponseComplete);

    if (preferencesUtilisateur.sons) {
        jouerSonNotificationSimple();
    }

    defilerVersBas();
}

function gererErreurEnvoi(error) {
    console.error('Erreur lors de l\'envoi:', error);

    if (error.name === 'AbortError') {
        ajouterMessage("Réponse interrompue.", 'bot');
    } else {
        let messageErreur = "Désolé, une erreur s'est produite. Veuillez réessayer dans un moment.";

        if (error.message.includes('401')) {
            messageErreur = "Erreur d'authentification API. Veuillez contacter l'administrateur.";
        } else if (error.message.includes('network') || error.message.includes('fetch')) {
            messageErreur = "Erreur de connexion. Vérifiez votre accès internet.";
        } else if (error.message.includes('quota') || error.message.includes('limit')) {
            messageErreur = "Limite d'utilisation atteinte. Veuillez réessayer plus tard.";
        }

        ajouterMessage(messageErreur, 'erreur');
    }
}

// ===== FONCTIONS UTILITAIRES =====
function arreterReponse() {
    if (controleurRequete && enAttente) {
        controleurRequete.abort();
    }
}

function reinitialiserInterfaceEnvoi() {
    saisieUtilisateur.value = '';
    btnEnvoyer.disabled = true;
    enAttente = true;
    autoResize(saisieUtilisateur);

    if (btnArreter) {
        btnArreter.style.display = 'inline-block';
    }
}

function finaliserEnvoi() {
    enAttente = false;
    btnEnvoyer.disabled = false;

    if (btnArreter) {
        btnArreter.style.display = 'none';
    }

    controleurRequete = null;
    saisieUtilisateur.focus();
    gererBoutonsFrappe();
}

function gererBoutonsFrappe() {
    const texte = saisieUtilisateur.value.trim();
    btnEnvoyer.disabled = !(texte.length > 0 && !enAttente);
    btnEnvoyer.style.opacity = btnEnvoyer.disabled ? '0.5' : '1';
}

function defilerVersBas() {
    if (preferencesUtilisateur.autoScroll && boiteChat) {
        boiteChat.scrollTop = boiteChat.scrollHeight;
    }
}

// ===== GESTION DU MARKDOWN AVEC TABLEAU NOIR =====
function convertirMarkdownEnHTML(texte) {
    if (!texte || texte.trim() === '') return '';

    // Échapper les caractères HTML d'abord
    texte = escapeHtml(texte);

    // Gérer les retours à la ligne
    texte = texte.replace(/([^\n])\n([^\n])/g, '$1<br>$2');

    // Gérer les titres
    texte = texte.replace(/^### (.*$)/gm, '<h3>$1</h3>');
    texte = texte.replace(/^## (.*$)/gm, '<h2>$1</h2>');
    texte = texte.replace(/^# (.*$)/gm, '<h1>$1</h1>');

    // Gras
    texte = texte.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    texte = texte.replace(/__([^_]+)__/g, '<strong>$1</strong>');

    // Italique
    texte = texte.replace(/\*([^*]+)\*/g, '<em>$1</em>');
    texte = texte.replace(/_([^_]+)_/g, '<em>$1</em>');

    // Code inline
    texte = texte.replace(/`([^`]+)`/g, '<code class="inline-code">$1</code>');

    // Listes
    texte = texte.replace(/^\s*[\-\*\+]\s+(.*)/gm, '<li>$1</li>');
    texte = texte.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');

    // Blocs de citation
    texte = texte.replace(/^>\s*(.*)/gm, '<blockquote>$1</blockquote>');

    // Blocs de code (remplacement amélioré)
    // Remplacer les blocs ```lang ... ```
    texte = texte.replace(/```+\s*([a-zA-Z0-9_-]+)?\s*\n?([\s\S]*?)```+/g, function (match, lang, code) {
        const language = (lang || 'text').toLowerCase();
        const codeContent = code.replace(/\r\n/g, '\n').replace(/\t/g, '    ').trim();
        const escapedCode = escapeHtml(codeContent);

        const lines = escapedCode.split('\n');
        let numberedCode = '';
        lines.forEach((line, index) => {
            numberedCode += `<div class="code-line">
            <span class="line-number">${index + 1}</span>
            <span class="line-content">${line === '' ? '&nbsp;' : line}</span>
        </div>`;
        });

        const encodedRaw = encodeURIComponent(codeContent);

        return `<div class="code-terminal" data-language="${language}" data-code-raw="${encodedRaw}">
                <div class="terminal-header">
                    <div class="terminal-dots">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                    </div>
                    <div class="terminal-title">
                        <i class="fas fa-code"></i>
                        ${language.toUpperCase()}
                    </div>
                    <button class="btn-copier-terminal" onclick="copierCodeTerminal(this)">
                        <i class="far fa-copy"></i> Copier
                    </button>
                </div>
                <div class="terminal-body">
                    <div class="code-lines">${numberedCode}</div>
                </div>
            </div>`;
    });

    // Gérer les paragraphes
    texte = texte.replace(/(\n\n|^)([^\n<].*?)(\n\n|$)/gs, function (match, p1, p2, p3) {
        if (p2.trim() === '') return match;
        if (p2.startsWith('<') && p2.endsWith('>')) return match;
        return p1 + '<p>' + p2 + '</p>' + p3;
    });

    return texte;
}

// Fonction pour copier le code du terminal
function copierCodeTerminal(bouton) {
    // chercher le parent .code-terminal
    const terminal = bouton.closest('.code-terminal');
    if (!terminal) return;

    const rawEncoded = terminal.getAttribute('data-code-raw') || '';
    const code = decodeURIComponent(rawEncoded);

    // animation bouton
    bouton.disabled = true;
    navigator.clipboard.writeText(code).then(() => {
        const originalHTML = bouton.innerHTML;
        bouton.innerHTML = '<i class="fas fa-check"></i> Copié!';
        bouton.style.backgroundColor = '#1cc88a';
        bouton.style.color = 'white';

        setTimeout(() => {
            bouton.innerHTML = originalHTML;
            bouton.style.backgroundColor = '';
            bouton.style.color = '';
            bouton.disabled = false;
        }, 1500);
    }).catch(err => {
        console.error('Erreur copie:', err);
        bouton.innerHTML = '<i class="fas fa-times"></i> Erreur';
        setTimeout(() => {
            bouton.innerHTML = '<i class="far fa-copy"></i> Copier';
            bouton.disabled = false;
        }, 1500);
    });
}


function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Décoder HTML entities (ex: &lt; &gt; &amp;) et récupérer texte brut
function htmlDecode(html) {
    const doc = new DOMParser().parseFromString(html, "text/html");
    return doc.documentElement.textContent;
}

function convertirMessagesExistants() {
    document.querySelectorAll('.contenu-message').forEach((message) => {
        // Récupérer le HTML affiché (celui généré par PHP via nl2br(htmlspecialchars(...)))
        const rawHtml = message.innerHTML || '';

        // Remplacer les <br> en retours à la ligne pour reformer le Markdown brut
        const withNewlines = rawHtml.replace(/<br\s*\/?>/gi, '\n');

        // Décoder les entités HTML (&lt; &gt; &amp; etc.) en texte brut
        const texteOriginal = htmlDecode(withNewlines).trim();

        // Ne convertir que si c'est un message du bot et contient des backticks
        if (message.closest('.message.bot') && /```/.test(texteOriginal)) {
            const texteHTML = convertirMarkdownEnHTML(texteOriginal);
            message.innerHTML = texteHTML;

            // Appliquer highlight.js après conversion (detection auto par ligne)
            setTimeout(() => {
                message.querySelectorAll('.code-terminal').forEach((terminal) => {
                    if (terminal.dataset.colored === '1') return;

                    terminal.querySelectorAll('.line-content').forEach((lineEl) => {
                        const text = lineEl.textContent || lineEl.innerText || '';
                        if (!text.trim()) return;
                        try {
                            const result = hljs.highlightAuto(text);
                            lineEl.innerHTML = result.value;
                        } catch (err) {
                            lineEl.textContent = text;
                        }
                    });

                    terminal.dataset.colored = '1';
                });
            }, 80);
        }
    });
}



// Appeler au chargement de la page
document.addEventListener('DOMContentLoaded', function () {
    convertirMessagesExistants();
});
// ===== BOUTON COPIE SPÉCIAL POUR CODE =====
function copierCode(bouton) {
    const code = bouton.getAttribute('data-code');
    const icon = bouton.querySelector('i');

    navigator.clipboard.writeText(code).then(() => {
        const originalHTML = bouton.innerHTML;
        bouton.innerHTML = '<i class="fas fa-check"></i> Copié!';
        bouton.style.backgroundColor = '#1cc88a';

        setTimeout(() => {
            bouton.innerHTML = originalHTML;
            bouton.style.backgroundColor = '';
        }, 2000);
    }).catch(err => {
        console.error('Erreur copie:', err);
        bouton.innerHTML = '<i class="fas fa-times"></i> Erreur';
        setTimeout(() => {
            bouton.innerHTML = '<i class="far fa-copy"></i> Copier';
        }, 2000);
    });
}

// ===== BOUTON COPIE POUR MESSAGES =====
function ajouterBoutonCopie(messageElement, contenu) {
    const boutonCopie = document.createElement('button');
    boutonCopie.classList.add('btn-copier-message');
    boutonCopie.title = 'Copier le message';
    boutonCopie.innerHTML = '<i class="far fa-copy"></i>';

    boutonCopie.addEventListener('click', async function (e) {
        e.stopPropagation(); // Empêcher la propagation

        try {
            // Extraire le texte brut (sans HTML)
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = contenu;
            const texteBrut = tempDiv.textContent || tempDiv.innerText || '';

            await navigator.clipboard.writeText(texteBrut);

            // Animation de succès
            const icon = boutonCopie.querySelector('i');
            icon.className = 'fas fa-check';
            boutonCopie.style.backgroundColor = '#1cc88a';

            setTimeout(() => {
                icon.className = 'far fa-copy';
                boutonCopie.style.backgroundColor = '';
            }, 2000);
        } catch (err) {
            console.error('Erreur copie:', err);
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = contenu.replace(/<[^>]*>/g, '');
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            const icon = boutonCopie.querySelector('i');
            icon.className = 'fas fa-check';
            setTimeout(() => {
                icon.className = 'far fa-copy';
            }, 2000);
        }
    });

    const horodatage = messageElement.querySelector('.horodatage');
    if (horodatage) {
        // Créer un conteneur pour les boutons d'action
        let actionsContainer = horodatage.querySelector('.message-actions');
        if (!actionsContainer) {
            actionsContainer = document.createElement('div');
            actionsContainer.classList.add('message-actions');
            horodatage.appendChild(actionsContainer);
        }
        actionsContainer.appendChild(boutonCopie);
    }
}

// ===== NOTIFICATION SONORE =====
function jouerSonNotificationSimple() {
    try {
        const audioContext = new AudioContext();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Paramètres pour le son "popcorn" WhatsApp
        oscillator.frequency.value = 600;
        oscillator.type = 'sine';

        // Enveloppe ADSR très courte
        const now = audioContext.currentTime;
        gainNode.gain.setValueAtTime(0, now);
        gainNode.gain.linearRampToValueAtTime(0.3, now + 0.01);  // Attack très rapide
        gainNode.gain.exponentialRampToValueAtTime(0.1, now + 0.05); // Decay
        gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.1); // Release rapide

        // Changement de fréquence pour l'effet "pop"
        oscillator.frequency.setValueAtTime(600, now);
        oscillator.frequency.exponentialRampToValueAtTime(300, now + 0.1);

        oscillator.start(now);
        oscillator.stop(now + 0.1);

    } catch (error) {
        console.log('Notification sonore non disponible');
    }
}

// ===== GESTION DES MODALS =====
function ouvrirParametres() {
    document.getElementById('auto-scroll').checked = preferencesUtilisateur.autoScroll;
    document.getElementById('sons').checked = preferencesUtilisateur.sons;
    document.getElementById('vitesse-frappe').value = preferencesUtilisateur.vitesseFrappe;

    document.getElementById('modal-parametres').style.display = 'flex';
}

function fermerParametres() {
    preferencesUtilisateur.autoScroll = document.getElementById('auto-scroll').checked;
    preferencesUtilisateur.sons = document.getElementById('sons').checked;
    preferencesUtilisateur.vitesseFrappe = document.getElementById('vitesse-frappe').value;

    document.getElementById('modal-parametres').style.display = 'none';
}

function reinitialiserParametres() {
    document.getElementById('auto-scroll').checked = true;
    document.getElementById('sons').checked = true;
    document.getElementById('vitesse-frappe').value = 'normal';

    preferencesUtilisateur.autoScroll = true;
    preferencesUtilisateur.sons = true;
    preferencesUtilisateur.vitesseFrappe = 'normal';

    alert('Paramètres réinitialisés avec succès !');
}

function commencerNouvelleConversation() {
    // Fermer le dropdown
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(dropdown => {
        dropdown.style.display = 'none';
    });

    // Afficher la modal de confirmation
    const modal = document.getElementById('confirmation-modal');
    modal.style.display = 'flex';

    // Gestionnaire pour le bouton OK
    document.getElementById('confirm-ok').onclick = function () {
        // Rediriger pour créer une nouvelle conversation
        window.location.href = 'chatbot.php?nouvelle_conversation=1';
    };

    // Gestionnaire pour le bouton Annuler
    document.getElementById('confirm-cancel').onclick = function () {
        modal.style.display = 'none';
    };
}

// ===== GESTION DES SUGGESTIONS AMÉLIORÉE =====
function rafraichirSuggestions() {
    const suggestionsList = document.getElementById('suggestions-list');
    const btnRefresh = document.querySelector('.btn-refresh-suggestions');

    // Animation de rotation améliorée
    btnRefresh.style.transform = 'rotate(180deg)';
    btnRefresh.style.transition = 'transform 0.5s ease';

    fetch('includes/get_suggestions.php')
        .then(response => response.json())
        .then(suggestions => {
            suggestionsList.innerHTML = '';
            suggestions.forEach(suggestion => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = suggestion;
                div.onclick = () => utiliserSuggestion(suggestion, div);
                suggestionsList.appendChild(div);
            });

            // Réinitialiser la rotation
            setTimeout(() => {
                btnRefresh.style.transform = 'rotate(0deg)';
            }, 500);
        })
        .catch(error => {
            console.error('Erreur chargement suggestions:', error);
            btnRefresh.style.transform = 'rotate(0deg)';
        });
}

function utiliserSuggestion(question, element) {
    document.getElementById('saisie-utilisateur').value = question;
    document.getElementById('saisie-utilisateur').focus();
    autoResize(document.getElementById('saisie-utilisateur'));
    gererBoutonsFrappe();

    // Effet visuel amélioré sur la suggestion utilisée
    element.classList.add('utilisee');

    // Petit délai avant de masquer toutes les suggestions
    setTimeout(() => {
        cacherSuggestionsAvecAnimation();
    }, 1000);
}

function cacherSuggestionsAvecAnimation() {
    const suggestions = document.getElementById('suggestions-questions');
    if (suggestions) {
        suggestions.classList.add('disparait');
        setTimeout(() => {
            suggestions.style.display = 'none';
        }, 500);
    }
}

function cacherSuggestions() {
    const suggestions = document.getElementById('suggestions-questions');
    if (suggestions) {
        suggestions.classList.add('avec-messages');
    }
}

// Initialisation des écouteurs de modals
document.addEventListener('DOMContentLoaded', function () {
    const modals = document.querySelectorAll('.modal-custom');
    modals.forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    document.querySelector('#confirmation-modal .close-modal').addEventListener('click', function () {
        document.getElementById('confirmation-modal').style.display = 'none';
    });
});

// Afficher les suggestions au chargement si pas de messages
document.addEventListener('DOMContentLoaded', function () {
    const messagesExistants = document.querySelectorAll('.message');
    if (messagesExistants.length === 0) {
        // Les suggestions sont déjà visibles par défaut
        console.log('Aucun message existant, suggestions affichées');
    } else {
        cacherSuggestions();
    }
});



console.log('Script TDSI ChatBot chargé avec succès - Prêt à fonctionner avec streaming');