"use client";

import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import {
  Mail,
  Phone,
  MapPin,
  Send,
  User,
  MessageSquare,
  Car,
  ArrowRight,
  CheckCircle,
} from "lucide-react";

const contactMethods = [
  {
    icon: Mail,
    title: "Email Us",
    description: "We reply within a few hours",
    value: "sales@autoprideldn.co.uk",
    link: "mailto:sales@autoprideldn.co.uk",
  },
  {
    icon: Phone,
    title: "Call Us",
    description: "Speak to our sales team",
    value: "+44 7771 406382",
    link: "tel:+447771406382",
  },
  {
    icon: MapPin,
    title: "Visit Us",
    description: "See the cars in person",
    value: "Sunbury-on-Thames",
    link: "#",
  },
];

interface FormData {
  name: string;
  email: string;
  carInterest: string;
  message: string;
}

export default function InquiryForm() {
  const [formData, setFormData] = useState<FormData>({
    name: "",
    email: "",
    carInterest: "",
    message: "",
  });
  const [website, setWebsite] = useState("");
  const [renderedAt] = useState(() => Date.now());
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [submitError, setSubmitError] = useState<string | null>(null);

  const handleInputChange = (field: keyof FormData, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
    if (errors[field]) {
      setErrors((prev) => ({ ...prev, [field]: "" }));
    }
  };

  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.name.trim()) {
      newErrors.name = "Name is required";
    }

    if (!formData.email.trim()) {
      newErrors.email = "Email is required";
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = "Please enter a valid email";
    }

    if (!formData.message.trim()) {
      newErrors.message = "Message is required";
    } else if (formData.message.trim().length < 10) {
      newErrors.message = "Message must be at least 10 characters";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateForm()) return;

    setIsSubmitting(true);
    setSubmitError(null);

    try {
      const response = await fetch("/backend/api/enquiries.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          ...formData,
          website,
          renderedAt,
        }),
      });

      if (!response.ok) {
        const data = await response.json().catch(() => null);
        throw new Error(data?.error || "Something went wrong. Please try again.");
      }

      setIsSubmitted(true);
    } catch {
      setSubmitError(
        "Couldn't send your enquiry right now. Please try again or call us directly."
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const fadeInUp = {
    hidden: { opacity: 0, y: 40 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.6, ease: "easeOut" as const },
    },
  };

  const staggerContainer = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: { staggerChildren: 0.15 },
    },
  };

  return (
    <section className="relative py-24 bg-[#0F0F12] text-white overflow-hidden">
      <motion.div
        className="relative z-10 max-w-6xl mx-auto px-6"
        variants={staggerContainer}
        initial="hidden"
        whileInView="visible"
        viewport={{ once: true, margin: "-100px" }}
      >
        <motion.div className="text-center mb-16" variants={fadeInUp}>
          <h2 className="text-4xl sm:text-5xl font-bold mb-4">
            Enquire About a <span className="text-[#00FFCC]">Car</span>
          </h2>
          <p className="text-lg text-gray-400 max-w-2xl mx-auto">
            Tell us what you&apos;re looking for and our team will get back to
            you shortly.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          <motion.div variants={fadeInUp}>
            <AnimatePresence mode="wait">
              {!isSubmitted ? (
                <motion.form
                  key="form"
                  onSubmit={handleSubmit}
                  className="space-y-5"
                  exit={{ opacity: 0, y: -20 }}
                  transition={{ duration: 0.3 }}
                >
                  <input
                    type="text"
                    name="website"
                    value={website}
                    onChange={(e) => setWebsite(e.target.value)}
                    tabIndex={-1}
                    autoComplete="off"
                    aria-hidden="true"
                    style={{
                      position: "absolute",
                      left: "-9999px",
                      width: "1px",
                      height: "1px",
                      opacity: 0,
                    }}
                  />

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div className="relative">
                      <User className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-500" />
                      <input
                        type="text"
                        placeholder="Your Name"
                        value={formData.name}
                        onChange={(e) =>
                          handleInputChange("name", e.target.value)
                        }
                        className={`w-full pl-10 pr-4 py-3.5 bg-[#18181C] border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#00FFCC] transition-colors ${
                          errors.name ? "border-red-400" : "border-white/10"
                        }`}
                      />
                      {errors.name && (
                        <p className="text-red-400 text-sm mt-1.5">
                          {errors.name}
                        </p>
                      )}
                    </div>

                    <div className="relative">
                      <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-500" />
                      <input
                        type="email"
                        placeholder="Email Address"
                        value={formData.email}
                        onChange={(e) =>
                          handleInputChange("email", e.target.value)
                        }
                        className={`w-full pl-10 pr-4 py-3.5 bg-[#18181C] border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#00FFCC] transition-colors ${
                          errors.email ? "border-red-400" : "border-white/10"
                        }`}
                      />
                      {errors.email && (
                        <p className="text-red-400 text-sm mt-1.5">
                          {errors.email}
                        </p>
                      )}
                    </div>
                  </div>

                  <div className="relative">
                    <Car className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-500" />
                    <input
                      type="text"
                      placeholder="Car you're interested in (optional)"
                      value={formData.carInterest}
                      onChange={(e) =>
                        handleInputChange("carInterest", e.target.value)
                      }
                      className="w-full pl-10 pr-4 py-3.5 bg-[#18181C] border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#00FFCC] transition-colors"
                    />
                  </div>

                  <div className="relative">
                    <MessageSquare className="absolute left-3 top-4 h-5 w-5 text-gray-500" />
                    <textarea
                      placeholder="Your message..."
                      rows={5}
                      value={formData.message}
                      onChange={(e) =>
                        handleInputChange("message", e.target.value)
                      }
                      className={`w-full pl-10 pr-4 py-3.5 bg-[#18181C] border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-[#00FFCC] transition-colors resize-none ${
                        errors.message ? "border-red-400" : "border-white/10"
                      }`}
                    />
                    {errors.message && (
                      <p className="text-red-400 text-sm mt-1.5">
                        {errors.message}
                      </p>
                    )}
                  </div>

                  {submitError && (
                    <p className="text-red-400 text-sm">{submitError}</p>
                  )}

                  <motion.button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full relative group overflow-hidden bg-[#00FFCC] text-black font-semibold py-3.5 px-6 rounded-xl transition-all disabled:opacity-50"
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                  >
                    <span className="relative flex items-center justify-center gap-2">
                      {isSubmitting ? (
                        <motion.div
                          className="w-5 h-5 border-2 border-black/30 border-t-black rounded-full"
                          animate={{ rotate: 360 }}
                          transition={{
                            duration: 1,
                            repeat: Infinity,
                            ease: "linear",
                          }}
                        />
                      ) : (
                        <>
                          <Send className="h-5 w-5" />
                          Send Enquiry
                          <ArrowRight className="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </>
                      )}
                    </span>
                  </motion.button>
                </motion.form>
              ) : (
                <motion.div
                  key="success"
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="text-center py-12"
                >
                  <motion.div
                    className="w-16 h-16 rounded-full bg-[#00FFCC]/10 border border-[#00FFCC]/30 flex items-center justify-center mx-auto mb-5"
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    transition={{ delay: 0.15, type: "spring", stiffness: 200 }}
                  >
                    <CheckCircle className="w-8 h-8 text-[#00FFCC]" />
                  </motion.div>
                  <h3 className="text-2xl font-bold text-white mb-3">
                    Enquiry Sent!
                  </h3>
                  <p className="text-gray-400 mb-6">
                    Thanks for reaching out — our team will contact you
                    shortly.
                  </p>
                  <button
                    onClick={() => {
                      setIsSubmitted(false);
                      setFormData({
                        name: "",
                        email: "",
                        carInterest: "",
                        message: "",
                      });
                    }}
                    className="px-6 py-3 bg-[#18181C] border border-white/10 rounded-xl text-white hover:bg-white/5 transition-colors"
                  >
                    Send Another Enquiry
                  </button>
                </motion.div>
              )}
            </AnimatePresence>
          </motion.div>

          <motion.div className="space-y-5" variants={fadeInUp}>
            {contactMethods.map((method) => (
              <motion.a
                key={method.title}
                href={method.link}
                className="block p-5 bg-[#18181C] rounded-2xl border border-white/10 hover:border-[#00FFCC]/30 transition-colors group"
                whileHover={{ scale: 1.02, y: -2 }}
              >
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 rounded-xl bg-[#00FFCC]/10 border border-[#00FFCC]/20 flex items-center justify-center">
                    <method.icon className="w-6 h-6 text-[#00FFCC]" />
                  </div>
                  <div className="flex-1">
                    <h4 className="text-lg font-semibold text-white">
                      {method.title}
                    </h4>
                    <p className="text-gray-500 text-sm mb-1">
                      {method.description}
                    </p>
                    <p className="text-white font-medium">{method.value}</p>
                  </div>
                  <ArrowRight className="w-5 h-5 text-gray-600 group-hover:text-[#00FFCC] group-hover:translate-x-1 transition-all" />
                </div>
              </motion.a>
            ))}
          </motion.div>
        </div>
      </motion.div>
    </section>
  );
}
