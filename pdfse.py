import os
import fitz  # PyMuPDF
import json
import nltk
from collections import Counter

# ───────────── SETTINGS ─────────────
PDF_FOLDER = "pdfs"  # Folder containing PDFs
OUTPUT_JSON = "pdf_index.json"
IMAGE_FOLDER = "images"
KEYWORD_COUNT = 50  # Number of keywords per PDF
MIN_IMAGE_SIZE_KB = 50  # Ignore images smaller than this
SUMMARY_WORD_LIMIT = 250

nltk.download("punkt")
nltk.download("averaged_perceptron_tagger")
nltk.download("stopwords")

STOPWORDS = set(nltk.corpus.stopwords.words("english"))

CUSTOM_STOPWORDS = {
    "file", "study", "result", "fig", "data", "method", "page",
    "article", "difference", "change", "author", "figure", "paper", "differences", "results",
    "research", "section", "table", "analysis", "common", "result", "condition", "value", "factor", "factors", "control", "effect", "effects", "change", "changes", "location", 
}


# ───────────── HELPERS ─────────────
def extract_text_from_pdf(pdf_path):
    doc = fitz.open(pdf_path)
    text = ""
    for page in doc:
        text += page.get_text()
    return text


def generate_summary(text, word_limit=SUMMARY_WORD_LIMIT):
    """Create a short HTML formatted summary."""
    sentences = nltk.sent_tokenize(text)
    summary_text = " ".join(sentences[:min(len(sentences), 10)])  # Approximation
    words = summary_text.split()
    if len(words) > word_limit:
        summary_text = " ".join(words[:word_limit]) + "..."
    return "<p>" + summary_text.replace("\n", "<br>") + "</p>"



def extract_keywords(text, top_n=KEYWORD_COUNT):
    words = nltk.word_tokenize(text.lower())
    words = [
        w for w in words
        if w.isalpha() and w not in STOPWORDS and w not in CUSTOM_STOPWORDS and len(w) >= 3
    ]
    tagged = nltk.pos_tag(words)
    nouns = [word for word, pos in tagged if pos.startswith("NN")]  # nouns
    freq = Counter(nouns)
    return [kw for kw, _ in freq.most_common(top_n)]

def extract_images(pdf_path, min_size_kb=MIN_IMAGE_SIZE_KB):
    images = []
    pdf_name = os.path.splitext(os.path.basename(pdf_path))[0]
    doc = fitz.open(pdf_path)
    for page_num, page in enumerate(doc):
        for img_index, img in enumerate(page.get_images(full=True)):
            try:
                xref = img[0]
                pix = fitz.Pixmap(page.parent, xref)
                if pix.n - pix.alpha < 4:  # RGB
                    img_path = f"{IMAGE_FOLDER}/{pdf_name}_page{page_num+1}_img{img_index+1}.png"
                    pix.save(img_path)
                    if os.path.getsize(img_path) >= min_size_kb * 1024:
                        images.append(img_path)
                    else:
                        os.remove(img_path)
                pix = None
            except Exception as e:
                print(f"⚠️ Failed to extract image {img_index+1} on page {page_num+1}: {e}")
    return images


# ───────────── HELPERS ─────────────
def extract_title(pdf_path):
    """Extract PDF title from metadata, fallback to first page heading."""
    doc = fitz.open(pdf_path)
    title = doc.metadata.get("title")
    if title and title.strip():
        return title.strip()

    # Fallback: Extract first heading from first page
    if len(doc) > 0:
        first_page_text = doc[0].get_text("text")
        lines = first_page_text.split("\n")
        for line in lines:
            line = line.strip()
            if len(line) > 5 and any(c.isalpha() for c in line):
                return line

    return os.path.basename(pdf_path)
    
def process_pdf(pdf_path):
    text = extract_text_from_pdf(pdf_path)
    summary = generate_summary(text)
    title = extract_title(pdf_path)
    images = extract_images(pdf_path)
    keywords = extract_keywords(title + ' ' + summary)

    filename = os.path.basename(pdf_path)

    return {
        "filename": filename,          # original file name
        "title": title,
        "summary": summary,            # HTML formatted summary
        "images": images,              # list of extracted image paths
        "keywords": keywords,          # list of keywords/nouns
    }


# ───────────── MAIN ─────────────
if __name__ == "__main__":
    os.makedirs(IMAGE_FOLDER, exist_ok=True)
    index = []

    for file in os.listdir(PDF_FOLDER):
        if file.lower().endswith(".pdf"):
            print(f"Processing: {file}")
            try:
                pdf_entry = process_pdf(os.path.join(PDF_FOLDER, file))
                index.append(pdf_entry)
            except Exception as e:
                print(f"❌ Failed processing {file}: {e}")

    with open(OUTPUT_JSON, "w", encoding="utf-8") as f:
        json.dump(index, f, ensure_ascii=False, indent=2)

    print(f"✅ PDF index saved to {OUTPUT_JSON}")
