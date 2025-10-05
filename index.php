<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NASA Knowledge Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        background: url('back1.jpg') no-repeat center center fixed;
        background-size: cover;
        color: white;
        font-family: Arial, sans-serif;
        overflow-x: hidden; /* Prevent horizontal scroll from particles */
    }
    /* Particle Background Styling */
    #particles-js {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
    .overlay {
        background-color: rgba(0, 0, 20, 0.8);
        min-height: 100vh;
        padding: 20px;
		z-index: 1;
    }
    #searchInput {
        width: 70%;
        font-size: 1.5rem;
        padding: 10px;
    }
    #searchButton {
        padding: 10px 20px;
        font-size: 1.2rem;
    }
    .result-card {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    .result-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #00bfff;
        text-decoration: none;
    }
    .result-title:hover {
        text-decoration: underline;
    }
    .summary {
        font-size: 1.1rem;
        margin-top: 10px;
    }
    .context {
        font-size: 1rem;
        margin-top: 5px;
        color: #ccc;
    }
    .pdf-link {
        display: block;
        margin-top: 10px;
        color: #00ffcc;
    }
    .keywords-badges {
        margin-top: 10px;
    }
    .keywords-badges .badge {
        margin: 2px;
        cursor: pointer;
    }
    footer {
        text-align: center;
        padding: 20px;
        background: rgba(0,0,0,0.6);
        font-size: 0.9rem;
    }
    .graph-container {
        background: rgba(0,0,0,0.3);
        border-radius: 10px;
        padding: 20px;
    }
    @media (min-width: 768px) {
        .main-content {
            display: flex;
        }
        .results-section {
            flex: 2;
            margin-right: 20px;
        }
        .graph-section {
            flex: 1;
        }
    }
    @media (max-width: 767px) {
        .results-section, .graph-section {
            width: 100%;
            margin-bottom: 20px;
        }
    }
    .node text {
        fill: white;
        font-size: 12px;
        pointer-events: none;
    }
	
	
	
    /* --- Modal Styles: Details Tab --- */
    .modal-content {
        background-color: #448; /* Dark panel background */
        border: 2px solid var(--accent-light);
        border-radius: 10px;
        box-shadow: 0 0 25px rgba(91, 154, 255, 0.5);
        color: var(--text-light);
    }
    .modal-header {
        border-bottom: 1px solid var(--accent-light);
    }
    .modal-title {
        color: white;
        font-weight: 700;
    }
    .modal-footer {
        border-top: none;
    }
    .close-btn {
        background-color: var(--accent-light);
        color: var(--primary-dark);
        font-weight: 600;
    }
    .close-btn:hover {
        background-color: #7AAFFF;
    }	
	

    /* --- Chatbot Styles: Integrated Look --- */
    #open-chatbot {
        padding: 10px 20px;
        font-size: 1.2rem;
    }
    #chatbot-container {
        background-color: var(--primary-dark);
        border: 1px solid var(--success-chat);
		    position: fixed;
    top: 150px;
    left: 30px;
    width: 60%;
    height: 70%;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    z-index: 1000;
    display: none;
    flex-direction: column;
    overflow: hidden;
    }
    #chatbot-header {
        background-color: #66d;
        border-bottom: 1px solid var(--success-chat);
    }
    #chatbot-body {
        background-color: #151D36; 
    }
    .bot-message {
        background-color: #448 !important;
        color: var(--text-light) !important;
    }
    .user-message {
        background-color: black !important;
        color: var(--primary-dark) !important;
    }
    #chatbot-footer {
        background-color: #66d;
    }
    #chatbot-input {
        background-color: #0A0A1F;
        border: 1px solid var(--secondary-dark);
        color: var(--text-light);
    }
    #chatbot-send-btn {
        background-color: var(--success-chat) !important;
    }
	
	@media (max-width: 768px) {
    #chatbot-container {
        top: 10vh;
        left: 5vw;
        width: 90%;
        height: 90%;
        border-radius: 0; /* remove rounded corners for full screen */
    }
    #chatbot-header {
        font-size: 1rem; /* slightly smaller title */
        padding: 8px;
    }
    #chatbot-footer {
        flex-direction: column;
        gap: 6px;
    }
    #chatbot-input {
        width: 100%;
        margin-right: 0;
    }
    #chatbot-send-btn {
        width: 100%;
    }
}
</style>
</head>
<body>


<div class="overlay">
    <div class="text-center mb-4">
        <h1>NASA Knowledge Dashboard</h1>
        <input type="text" id="searchInput" placeholder="Enter keywords..." />
        <button id="searchButton" class="btn btn-primary">Search</button>
		<button id="open-chatbot" class="btn btn-primary" >Chat &#x1F4AC;</button>
    </div>


		        
        <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 mt-3 me-3" 
                data-bs-toggle="modal" data-bs-target="#detailsModal" 
                style="border-color: var(--accent-light); color: var(--accent-light);">
            Details &#x2139;
        </button>
		
    <div class="main-content">
        <div class="results-section container">
            <div id="results"></div>
        </div>

        <div class="graph-section container">
            <h3>Knowledge Browser</h3>
            <div id="balloonGraph" class="graph-container" style="width:100%; height:400px;"></div>
            <h4 class="mt-4">Other Keywords</h4>
            <div id="otherKeywords" class="keywords-badges"></div>
        </div>
    </div>

<div id="particles-js"></div> 
	
</div>



<div id="chatbot-container" style="">
    <div id="chatbot-header" style="color: white; padding: 10px; border-top-left-radius: 10px; border-top-right-radius: 10px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
        <h5 style="margin: 0; font-weight: 600;">Knowledge Bot</h5>
        <span id="close-chatbot" style="font-size: 1.5rem; line-height: 1; cursor: pointer;">&times;</span>
    </div>

    <div id="chatbot-body" style="flex-grow: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
        <div class="message bot-message" style="max-width: 80%; padding: 8px 12px; border-radius: 15px; border-bottom-left-radius: 2px; align-self: flex-start;">
            <p>Hello! I can answer questions about the documents on this page. Try asking about **immune proteins** or the **Drosophila gene** for a quick answer!</p>
        </div>
    </div>

    <div id="chatbot-footer" style="padding: 10px; display: flex;">
        <input type="text" id="chatbot-input" placeholder="Ask me a question..." style="flex-grow: 1; padding: 8px; border-radius: 5px; margin-right: 8px;">
        <button id="chatbot-send-btn" style="padding: 8px 12px; color: white; border: none; border-radius: 5px; cursor: pointer;">Send</button>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="2" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
		
		
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Team STELLA LUCIDA Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(1) brightness(2);"></button>
            </div>
            <div class="modal-body">
                <p><strong style="color: var(--accent-light);">Creator:</strong> Team STELLA LUCIDA</p>
                <p><strong style="color: var(--accent-light);">Members:</strong></p>
                <ul>
                    <li>Shriya Shah</li>
                    <li>Shriyan Tandale</li>
                    <li>Radha Odhekar</li>
                    <li>Tirth Mendapara</li>
                </ul>
                <p><strong style="color: var(--accent-light);">More About Us:</strong> -</p>
                <p><strong style="color: var(--accent-light);">MAILS:</strong></p>
                <ul>
                    <li><a href="mailto:samplayerpro2930@gmail.com" style="color: var(--text-light);">samplayerpro2930@gmail.com</a></li>
                    <li><a href="mailto:shriyashah152011@gmail.com" style="color: var(--text-light);">shriyashah152011@gmail.com</a></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn close-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<footer>
    Wisdom High International School, Nashik | TEAM Stella Lucida: Shriya Shah, Tirth Mendapara, Radha Odhekar, Shriyan Tandale
</footer>

<script src="https://d3js.org/d3.v7.min.js"></script>
<script>
let uniqueKeywords = {};
let currentKeywords = [];

function fetchResults(keywords = "") {
	document.getElementById('chatbot-container').style.display = 'none';
	
    fetch('search.php?keywords=' + encodeURIComponent(keywords))
    .then(res => res.json())
    .then(data => {
        uniqueKeywords = data.unique_keywords;
        displayResults(data.results);
        renderKeywordGraph(uniqueKeywords);
    });
}

function displayResults(results) {
    const resultsDiv = document.getElementById("results");
    resultsDiv.innerHTML = "";

    if (results.length === 0) {
        resultsDiv.innerHTML = "<p>No results found.</p>";
        return;
    }

    results.forEach(entry => {
        const card = document.createElement("div");
        card.className = "result-card";

        // Summary: show only first ~5 lines initially
        const summaryHTML = `
            <div class="summary-container" style="font-size: 1rem; cursor: pointer;">
                <div class="summary-preview" style="max-height: 6em; overflow: hidden;">${entry.summary}</div>
                <div class="summary-full" style="display:none;">${entry.summary}</div>
                <span class="read-more text-info" style="cursor:pointer;">Read more...</span>
            </div>
        `;

        // All images
        let imagesHTML = "";
        if (entry.images && Array.isArray(entry.images)) {
            entry.images.forEach(img => {
                imagesHTML += `<img src="${img}" style="max-width:100px; margin:5px; border-radius:5px;" />`;
            });
        }

        card.innerHTML = `
            <a class="result-title" href="pdfs/${entry.filename}" target="_blank">${entry.title}</a>
            ${summaryHTML}
            <div class="images-section">${imagesHTML}</div>
            <div class="pdf-link">
                <a href="pdfs/${entry.filename}" target="_blank">Download PDF: ${entry.filename}</a>
            </div>
        `;

        resultsDiv.appendChild(card);

        // "Read more" click event
        const summaryContainer = card.querySelector(".summary-container");
        const readMore = summaryContainer.querySelector(".read-more");
        const summaryPreview = summaryContainer.querySelector(".summary-preview");
        const summaryFull = summaryContainer.querySelector(".summary-full");

        readMore.addEventListener("click", () => {
            const isVisible = summaryFull.style.display === "block";
            summaryFull.style.display = isVisible ? "none" : "block";
            summaryPreview.style.display = isVisible ? "block" : "none";
            readMore.textContent = isVisible ? "Read more..." : "Read less";
        });
    });
}


function renderKeywordGraph(keywordsData) {
    const keywordEntries = Object.entries(keywordsData);
    keywordEntries.sort((a, b) => b[1] - a[1]);

    const topKeywords = keywordEntries.slice(0, 40);
    const otherKeywords = keywordEntries.slice(40, 290);

    const width = document.getElementById("balloonGraph").clientWidth;
    const height = document.getElementById("balloonGraph").clientHeight;

    document.getElementById("balloonGraph").innerHTML = "";

    const svg = d3.select("#balloonGraph").append("svg")
        .attr("width", width)
        .attr("height", height);

    // Color scale for balloons
    const colorScale = d3.scaleOrdinal(d3.schemeCategory10);

    const maxCount = d3.max(topKeywords, d => d[1]);
    const minCount = d3.min(topKeywords, d => d[1]);

    const radiusScale = d3.scaleSqrt()
        .domain([minCount, maxCount])
        .range([15, 60]); // Bigger range difference for stronger variation

    const nodes = topKeywords.map(d => ({
        id: d[0],
        value: d[1],
        radius: radiusScale(d[1]),
        color: colorScale(d[0])
    }));

    const simulation = d3.forceSimulation(nodes)
        .force("center", d3.forceCenter(width / 2, height / 2))
        .force("charge", d3.forceManyBody().strength(5))
        .force("collision", d3.forceCollide().radius(d => d.radius + 2))
        .on("tick", ticked);

    const node = svg.selectAll("g")
        .data(nodes)
        .enter()
        .append("g")
        .attr("class", "node")
        .style("cursor", "pointer")
        .on("click", function(event, d) {
    addKeywordAndSearch(d.id);
});

    node.append("circle")
        .attr("r", d => d.radius)
        .attr("fill", d => d.color)
        .attr("stroke", "#fff")
        .attr("stroke-width", 1.5);

    node.append("text")
        .text(d => d.id)
        .attr("text-anchor", "middle")
        .attr("dy", ".3em")
        .attr("fill", "#fff")
        .style("pointer-events", "none")
        .style("font-size", "12px");

    node.append("title")
        .text(d => `${d.id}: ${d.value}`); // tooltip

    function ticked() {
        node.attr("transform", d => `translate(${d.x},${d.y})`);
    }

    // Other keywords badges
    const otherDiv = document.getElementById("otherKeywords");
    otherDiv.innerHTML = "";
otherKeywords.forEach(([kw, count]) => {
    const badge = document.createElement("span");
    badge.className = "badge bg-secondary";
    badge.innerText = `${kw} (${count})`;
    badge.onclick = () => {
        currentKeywords = [kw]; // replace search keywords
        document.getElementById("searchInput").value = kw;
		fetchResults(currentKeywords.join(" "));
    };
    otherDiv.appendChild(badge);
});
}



function addKeywordAndSearch(keyword) {
    if (!currentKeywords.includes(keyword)) {
        currentKeywords.push(keyword);
    }
    document.getElementById("searchInput").value = currentKeywords.join(" ");
    fetchResults(currentKeywords.join(" "));
}

document.getElementById("searchButton").onclick = function() {
    const keywords = document.getElementById("searchInput").value;
    currentKeywords = keywords.trim() ? keywords.trim().split(/\s+/) : [];
    fetchResults(currentKeywords.join(" "));
};

document.getElementById("searchInput").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        document.getElementById("searchButton").click();
    }
});
// Default empty search on load
window.onload = () => fetchResults();
</script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script> 
<script>
    // --- PARTICLE.JS CONFIGURATION (Starfield Effect) ---
    particlesJS('particles-js', {
        "particles": {
            "number": {
                "value": 80,
                "density": {
                    "enable": true,
                    "value_area": 800
                }
            },
            "color": {
                "value": "#ffffff"
            },
            "shape": {
                "type": "circle",
                "stroke": {
                    "width": 0,
                    "color": "#000000"
                },
                "polygon": {
                    "nb_sides": 5
                }
            },
            "opacity": {
                "value": 0.8,
                "random": false,
                "anim": {
                    "enable": false,
                    "speed": 1,
                    "opacity_min": 0.1,
                    "sync": false
                }
            },
            "size": {
                "value": 3,
                "random": true,
                "anim": {
                    "enable": false,
                    "speed": 40,
                    "size_min": 0.1,
                    "sync": false
                }
            },
            "line_linked": {
                "enable": false,
            },
            "move": {
                "enable": true,
                "speed": 1,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {
                    "enable": false,
                    "rotateX": 600,
                    "rotateY": 1200
                }
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "repulse"
                },
                "onclick": {
                    "enable": true,
                    "mode": "push"
                },
                "resize": true
            },
            "modes": {
                "grab": {
                    "distance": 400,
                    "line_linked": {
                        "opacity": 1
                    }
                },
                "bubble": {
                    "distance": 400,
                    "size": 40,
                    "duration": 2,
                    "opacity": 8,
                    "speed": 3
                },
                "repulse": {
                    "distance": 100,
                    "duration": 0.4
                },
                "push": {
                    "particles_nb": 4
                },
                "remove": {
                    "particles_nb": 2
                }
            }
        },
        "retina_detect": true
    });
</script>
<script>
var CHATBOT_KNOWLEDGE_BASE = <?php
    $data = include('keywords.php');
    // (filtering code here like I wrote earlier)
    echo json_encode($data);
?>;


    // --- Chatbot Logic ---
    function createMessageElement(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        if (sender === 'user') {
            messageDiv.classList.add('user-message');
            messageDiv.style.cssText = 'max-width: 80%; padding: 8px 12px; border-radius: 15px; border-bottom-right-radius: 2px; align-self: flex-end;';
        } else {
            messageDiv.classList.add('bot-message');
            messageDiv.style.cssText = 'max-width: 80%; padding: 8px 12px; border-radius: 15px; border-bottom-left-radius: 2px; align-self: flex-start;';
        }
        messageDiv.innerHTML = `<p>${text}</p>`;
        return messageDiv;
    }

    function generateChatbotResponse(userQuestion) {
        const lowerQuestion = userQuestion.toLowerCase().replace(/[^\w\s]/g, '');
        const questionWords = lowerQuestion.split(/\s+/).filter(word => word.length > 2); 

        let bestMatch = null;
        let maxMatchScore = 0;

        for (const doc of CHATBOT_KNOWLEDGE_BASE) {
            let currentScore = 0;
            const docSummaryLower = doc.summary.toLowerCase().replace(/[^\w\s]/g, '');

            const matchedKeywords = doc.keywords.filter(kw => lowerQuestion.includes(kw.toLowerCase()));
            currentScore += matchedKeywords.length * 5; 

            const matchedContextWords = questionWords.filter(qWord => docSummaryLower.includes(qWord));
            currentScore += matchedContextWords.length * 1; 

            if (currentScore > maxMatchScore) {
                maxMatchScore = currentScore;
                bestMatch = doc;
            }
        }

        if (maxMatchScore > 0) {
            const matchedKeywords = bestMatch.keywords.filter(kw => lowerQuestion.includes(kw.toLowerCase()));
            
            let response = `**The most relevant document is: ${bestMatch.title}.**<br><br>`;
            response += `**Summary:** ${bestMatch.summary}<br><br>`;
            response += `*This was found using the keywords: ${matchedKeywords.join(', ')}*`;
            return response;
        } else {
            const allKeywords = CHATBOT_KNOWLEDGE_BASE.flatMap(doc => doc.keywords.slice(0, 3)).filter((value, index, self) => self.indexOf(value) === index).slice(0, 10).join(', ');
            return `I'm sorry, I couldn't find a direct match for *"${userQuestion}"* in my current knowledge base. <br><br>My knowledge covers topics like: **${allKeywords}**, and more. Try rephrasing your question using these terms.`;
        }
    }


            document.addEventListener('DOMContentLoaded', () => {
        const chatBody = document.getElementById('chatbot-body');
        const chatInput = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send-btn');
        const openBtn = document.getElementById('open-chatbot');
        const closeBtn = document.getElementById('close-chatbot');
        const chatContainer = document.getElementById('chatbot-container');

        const sendMessage = () => {
            const userQuestion = chatInput.value.trim();
            if (userQuestion === '') return;

            chatBody.appendChild(createMessageElement(userQuestion, 'user'));
            chatBody.scrollTop = chatBody.scrollHeight;
            chatInput.value = '';

            setTimeout(() => {
                const botResponseText = generateChatbotResponse(userQuestion);
                chatBody.appendChild(createMessageElement(botResponseText, 'bot'));
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 500);
        };

        sendBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });

        openBtn.addEventListener('click', () => {
            chatContainer.style.display = 'flex';
        });

        closeBtn.addEventListener('click', () => {
            chatContainer.style.display = 'none';
        });
    });

</script>
</body>
</html>
