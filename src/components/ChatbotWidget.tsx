"use client";

import { useEffect, useRef, useState } from "react";
import { MessageCircle, X, Send } from "lucide-react";

interface ChatMessage {
  from: "bot" | "user";
  text: string;
}

interface ChatSettings {
  enabled: boolean;
  greeting: string;
}

export default function ChatbotWidget() {
  const [visible, setVisible] = useState(false);
  const [settings, setSettings] = useState<ChatSettings | null>(null);
  const [open, setOpen] = useState(false);
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [input, setInput] = useState("");
  const [sending, setSending] = useState(false);
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    fetch("/backend/api/settings.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load settings");
        return res.json();
      })
      .then(
        (data: {
          chatbot?: Partial<ChatSettings>;
          features?: Record<string, boolean>;
        }) => {
          const enabled =
            !!data.chatbot?.enabled && data.features?.chatbot_widget !== false;
          setVisible(enabled);
          setSettings({
            enabled,
            greeting:
              data.chatbot?.greeting ||
              "Hi! How can I help you today? Ask me about our cars, services, or location.",
          });
        }
      )
      .catch(() => {
        // Stay hidden on failure.
      });
  }, []);

  useEffect(() => {
    if (open && settings && messages.length === 0) {
      setMessages([{ from: "bot", text: settings.greeting }]);
    }
  }, [open, settings, messages.length]);

  useEffect(() => {
    scrollRef.current?.scrollTo({ top: scrollRef.current.scrollHeight });
  }, [messages, sending]);

  const sendMessage = async (text: string) => {
    const trimmed = text.trim();
    if (!trimmed || sending) return;

    setMessages((prev) => [...prev, { from: "user", text: trimmed }]);
    setInput("");
    setSuggestions([]);
    setSending(true);

    try {
      const res = await fetch("/backend/api/chatbot.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: trimmed }),
      });
      const data: { answer: string; suggestions: string[] } = await res.json();
      setMessages((prev) => [...prev, { from: "bot", text: data.answer }]);
      setSuggestions(data.suggestions || []);
    } catch {
      setMessages((prev) => [
        ...prev,
        {
          from: "bot",
          text: "Sorry, I couldn't reach our servers just now. Please try again shortly.",
        },
      ]);
    } finally {
      setSending(false);
    }
  };

  if (!visible) return null;

  return (
    <div className="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">
      {open && (
        <div className="w-80 max-w-[calc(100vw-3rem)] h-[26rem] bg-[#18181C] border border-white/10 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
          <div className="px-4 py-3 bg-[#0F0F12] border-b border-white/10 flex items-center justify-between">
            <span className="text-sm font-semibold text-white">
              Auto Pride London — Chat
            </span>
            <button
              onClick={() => setOpen(false)}
              aria-label="Close chat"
              className="text-gray-400 hover:text-white transition-colors"
            >
              <X className="w-4 h-4" />
            </button>
          </div>

          <div
            ref={scrollRef}
            className="flex-1 overflow-y-auto px-4 py-3 space-y-2"
          >
            {messages.map((m, i) => (
              <div
                key={i}
                className={`max-w-[85%] px-3 py-2 rounded-xl text-sm ${
                  m.from === "bot"
                    ? "bg-[#0F0F12] text-gray-200 self-start"
                    : "bg-[#00FFCC] text-black ml-auto"
                }`}
              >
                {m.text}
              </div>
            ))}
            {sending && (
              <div className="max-w-[85%] px-3 py-2 rounded-xl text-sm bg-[#0F0F12] text-gray-500">
                Typing...
              </div>
            )}
          </div>

          {suggestions.length > 0 && (
            <div className="px-3 pb-2 flex flex-wrap gap-2">
              {suggestions.map((s) => (
                <button
                  key={s}
                  onClick={() => sendMessage(s)}
                  className="text-xs px-3 py-1.5 rounded-full bg-[#0F0F12] border border-white/10 text-gray-300 hover:border-[#00FFCC]/40 hover:text-[#00FFCC] transition-colors"
                >
                  {s}
                </button>
              ))}
            </div>
          )}

          <form
            onSubmit={(e) => {
              e.preventDefault();
              sendMessage(input);
            }}
            className="p-3 border-t border-white/10 flex gap-2"
          >
            <input
              type="text"
              value={input}
              onChange={(e) => setInput(e.target.value)}
              placeholder="Type a message..."
              className="flex-1 bg-[#0F0F12] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-[#00FFCC]"
            />
            <button
              type="submit"
              disabled={sending}
              aria-label="Send"
              className="w-9 h-9 flex items-center justify-center rounded-lg bg-[#00FFCC] text-black disabled:opacity-50"
            >
              <Send className="w-4 h-4" />
            </button>
          </form>
        </div>
      )}

      <button
        onClick={() => setOpen((v) => !v)}
        aria-label={open ? "Close chat" : "Open chat"}
        className="w-14 h-14 rounded-full bg-[#00FFCC] text-black flex items-center justify-center shadow-lg hover:scale-105 transition-transform"
      >
        {open ? <X className="w-6 h-6" /> : <MessageCircle className="w-6 h-6" />}
      </button>
    </div>
  );
}
