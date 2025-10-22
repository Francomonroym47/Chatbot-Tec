export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ error: "Método no permitido" });
  }

  try {
    const { message } = req.body;
    if (!message) return res.status(400).json({ error: "Falta el parámetro 'message'" });

    // URL de tu Webhook en n8n
    const response = await fetch("https://franciscomonroy.app.n8n.cloud/webhook/4b90adba-3085-4032-b656-46017b6defd4", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message })
    });

    const data = await response.json();
    res.status(200).json(data);
  } catch (error) {
    console.error("Error en proxy:", error);
    res.status(500).json({ error: "Error interno del servidor" });
  }
}
