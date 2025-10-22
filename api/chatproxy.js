export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ reply: "Método no permitido" });
  }

  try {
    const { message } = req.body;
    if (!message) {
      return res.status(400).json({ reply: "⚠️ Mensaje vacío." });
    }

    const n8nURL = "https://franciscomonroy.app.n8n.cloud/webhook/4b90adba-3085-4032-b656-46017b6defd4";

    const response = await fetch(n8nURL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message }),
    });

    const data = await response.json();
    console.log("Respuesta N8N:", data);

    if (Array.isArray(data) && data[0]?.reply) {
      return res.status(200).json({ reply: data[0].reply });
    } else if (data.reply) {
      return res.status(200).json({ reply: data.reply });
    } else {
      return res.status(200).json({ reply: "No proxy" });
    }

  } catch (error) {
    console.error("Error proxy:", error);
    return res.status(500).json({ reply: "No se conectó en el proxy ❌" });
  }
}
