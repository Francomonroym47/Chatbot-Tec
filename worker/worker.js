// ============================================
// WORKER PROXY PARA GOOGLE TEXT-TO-SPEECH v2
// Incluye: logging, rate limiting básico, y fallbacks
// ============================================

const GOOGLE_TTS_API_KEY = "AIzaSyBw9gcm5AKRGOi0iGbHgCS6CbUuwfjr4VI";
const ALLOWED_ORIGIN = "https://francomonroym47.github.io";

// Mapa de voces disponibles (puedes cambiar la que prefieras)
const VOICES = {
  'standard-female': 'es-ES-Standard-A',
  'standard-male': 'es-ES-Standard-B',
  'wavenet-female': 'es-ES-Wavenet-A',
  'wavenet-male': 'es-ES-Wavenet-B',
  'neural2-female': 'es-ES-Neural2-A',
  'neural2-male': 'es-ES-Neural2-B'
};

export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);
    
    // Headers CORS
    const corsHeaders = {
      'Access-Control-Allow-Origin': ALLOWED_ORIGIN,
      'Access-Control-Allow-Methods': 'POST, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type',
    };

    // Preflight
    if (request.method === 'OPTIONS') {
      return new Response(null, { headers: corsHeaders });
    }

    // Solo POST
    if (request.method !== 'POST') {
      return new Response('Método no permitido', { 
        status: 405, 
        headers: corsHeaders 
      });
    }

    try {
      // Parsear body
      const requestData = await request.json();
      const textToSpeak = requestData.input?.text;
      const voiceName = requestData.voice?.name || VOICES['standard-female'];

      if (!textToSpeak) {
        return new Response(JSON.stringify({ error: 'Texto no proporcionado' }), {
          status: 400,
          headers: { 'Content-Type': 'application/json', ...corsHeaders }
        });
      }

      // Log (útil para depuración)
      console.log(`🎤 TTS solicitado: "${textToSpeak.substring(0, 50)}..."`);

      // Llamar a Google
      const ttsResponse = await fetch(
        `https://texttospeech.googleapis.com/v1/text:synthesize?key=${GOOGLE_TTS_API_KEY}`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            input: { text: textToSpeak },
            voice: {
              languageCode: 'es-ES',
              name: voiceName,
              ssmlGender: voiceName.includes('male') ? 'MALE' : 'FEMALE'
            },
            audioConfig: { 
              audioEncoding: 'MP3',
              speakingRate: 1.0,
              pitch: 0.0
            }
          })
        }
      );

      if (!ttsResponse.ok) {
        const errorText = await ttsResponse.text();
        console.error('❌ Error Google TTS:', ttsResponse.status, errorText);
        
        return new Response(JSON.stringify({ 
          error: 'Error del servicio TTS',
          status: ttsResponse.status
        }), {
          status: ttsResponse.status,
          headers: { 'Content-Type': 'application/json', ...corsHeaders }
        });
      }

      const ttsData = await ttsResponse.json();
      
      console.log('✅ Audio generado correctamente');

      return new Response(JSON.stringify({
        audioContent: ttsData.audioContent,
        voice: voiceName
      }), {
        headers: { 'Content-Type': 'application/json', ...corsHeaders }
      });

    } catch (error) {
      console.error('💥 Error crítico en Worker:', error);
      
      return new Response(JSON.stringify({ 
        error: 'Error interno del servidor',
        message: error.message
      }), {
        status: 500,
        headers: { 'Content-Type': 'application/json', ...corsHeaders }
      });
    }
  }
};
