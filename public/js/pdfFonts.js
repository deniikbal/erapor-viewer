// PDF Fonts Utility for DejaVu Sans
let fontsLoaded = false;
let fontLoadPromise = null;

// Font loading function
export async function loadDejaVuFonts(doc) {
    if (fontsLoaded) return true;
    if (fontLoadPromise) return fontLoadPromise;
    
    fontLoadPromise = (async () => {
        try {
            // Try to load DejaVu Sans fonts
            const fontPaths = {
                'DejaVuSans-normal': '/fonts/dejavu-sans/DejaVuSans.ttf',
                'DejaVuSans-bold': '/fonts/dejavu-sans/DejaVuSans-Bold.ttf',
                'DejaVuSansCondensed-normal': '/fonts/dejavu-sans/DejaVuSansCondensed.ttf',
                'DejaVuSansCondensed-bold': '/fonts/dejavu-sans/DejaVuSansCondensed-Bold.ttf'
            };
            
            for (const [fontKey, fontPath] of Object.entries(fontPaths)) {
                try {
                    const response = await fetch(fontPath);
                    if (response.ok) {
                        const fontArrayBuffer = await response.arrayBuffer();
                        const fontBase64 = arrayBufferToBase64(fontArrayBuffer);
                        
                        // Add font to jsPDF
                        const [fontName, fontStyle] = fontKey.split('-');
                        doc.addFileToVFS(`${fontName}.ttf`, fontBase64);
                        doc.addFont(`${fontName}.ttf`, fontName, fontStyle);
                        
                        console.log(`✅ Loaded font: ${fontKey}`);
                    } else {
                        console.warn(`⚠️ Font not found: ${fontPath}`);
                    }
                } catch (error) {
                    console.warn(`⚠️ Error loading font ${fontKey}:`, error);
                }
            }
            
            fontsLoaded = true;
            return true;
        } catch (error) {
            console.error('❌ Error loading DejaVu fonts:', error);
            return false;
        }
    })();
    
    return fontLoadPromise;
}

// Helper function to convert ArrayBuffer to base64
function arrayBufferToBase64(buffer) {
    let binary = '';
    const bytes = new Uint8Array(buffer);
    const len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return btoa(binary);
}

// Set DejaVu font with fallback
export async function setDejaVuFont(doc, style = 'normal', condensed = true) {
    try {
        // Try to load fonts first
        const loaded = await loadDejaVuFonts(doc);
        
        if (loaded && fontsLoaded) {
            // Prioritize DejaVu Sans Condensed (which we have) over regular DejaVu Sans
            const fontName = condensed ? 'DejaVuSansCondensed' : 'DejaVuSans';
            
            // Check if the requested font is available, fallback to condensed if regular is not available
            try {
                doc.setFont(fontName, style);
                return true;
            } catch (fontError) {
                // If regular DejaVu Sans is not available, use condensed version
                if (!condensed) {
                    console.warn('⚠️ Regular DejaVu Sans not available, using DejaVu Sans Condensed');
                    doc.setFont('DejaVuSansCondensed', style);
                    return true;
                } else {
                    throw fontError;
                }
            }
        } else {
            // Fallback to Helvetica
            console.warn('⚠️ DejaVu fonts not available, using Helvetica fallback');
            doc.setFont('helvetica', style);
            return false;
        }
    } catch (error) {
        console.warn('⚠️ Font setting error, using Helvetica fallback:', error);
        doc.setFont('helvetica', style);
        return false;
    }
}

// Check if DejaVu fonts are available
export function isDejaVuAvailable() {
    return fontsLoaded;
}

// Preload fonts (call this early in your app)
export async function preloadDejaVuFonts() {
    const tempDoc = new jsPDF();
    return await loadDejaVuFonts(tempDoc);
}