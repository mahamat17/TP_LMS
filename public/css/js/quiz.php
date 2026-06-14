document.addEventListener('DOMContentLoaded', () => {
    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const resultDiv = document.getElementById('quizResult');
            
            resultDiv.innerHTML = "<span style='color:blue;'>Calcul de votre note...</span>";

            fetch('index.php?action=soumettre_quiz', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if(data.valide) {
                        resultDiv.style.color = "green";
                        resultDiv.innerHTML = `🎉 Évaluation Réussie ! Score : ${data.score}% - Leçon Suivante déverrouillée !`;
                        if(data.certificat) {
                            resultDiv.innerHTML += `<br><span style='color:gold;font-weight:bold;'>🏅 Félicitations ! Vous avez validé le module complet. Un certificat vous a été attribué !</span>`;
                        }
                    } else {
                        resultDiv.style.color = "red";
                        resultDiv.innerHTML = `❌ Échec (Score : ${data.score}%). Il vous faut au moins 50% pour valider.`;
                    }
                } else {
                    resultDiv.innerHTML = "Erreur : " + data.message;
                }
            }).catch(() => {
                resultDiv.innerHTML = "Erreur réseau.";
            });
        });
    }
});
