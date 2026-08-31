<div class="modern-footer">
  <div class="footer-content">
    <div class="footer-main">
      <div class="footer-logo">
        <i class="fa fa-graduation-cap footer-icon"></i>
        <span class="footer-brand">Online Clearance System</span>
      </div>
      <div class="footer-info">
        <p class="copyright-text">
          <i class="fa fa-copyright"></i> 
          Copyright 2026 • 
          <span class="developer-name">FINAL PROJECT CSE</span>
        </p>
        <p class="university-text">
          <i class="fa fa-university"></i>
          BULEHORA UNIVERSITY • All Rights Reserved
        </p>
      </div>
    </div>
    <div class="footer-decoration">
      <div class="decoration-line"></div>
      <div class="decoration-dots">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>
  </div>
  
  <!-- Floating particles background -->
  <div class="footer-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>
</div>

<style>
/* Modern Footer Styling with Advanced Animations */
:root {
  --footer-bg: linear-gradient(135deg, #2d3748 0%, #1a202c 50%, #2d3748 100%);
  --footer-text: #e2e8f0;
  --footer-text-muted: #a0aec0;
  --footer-accent: #667eea;
  --footer-secondary: #764ba2;
  --footer-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
  --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-footer {
  background: var(--footer-bg);
  color: var(--footer-text);
  padding: 32px 0 24px;
  margin-top: 40px;
  position: relative;
  overflow: hidden;
  box-shadow: var(--footer-shadow);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Animated background pattern */
.modern-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 75% 75%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
  animation: footerBackgroundShift 8s ease-in-out infinite;
  pointer-events: none;
}

@keyframes footerBackgroundShift {
  0%, 100% { 
    opacity: 0.3; 
    transform: scale(1) rotate(0deg); 
  }
  50% { 
    opacity: 0.6; 
    transform: scale(1.1) rotate(2deg); 
  }
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 2;
}

.footer-main {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 16px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
  animation: logoFloat 3s ease-in-out infinite;
}

.footer-icon {
  font-size: 24px;
  color: var(--footer-accent);
  animation: iconPulse 2s ease-in-out infinite;
}

.footer-brand {
  font-size: 18px;
  font-weight: 700;
  background: linear-gradient(135deg, var(--footer-accent), var(--footer-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

@keyframes logoFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-3px); }
}

@keyframes iconPulse {
  0%, 100% { 
    transform: scale(1); 
    filter: brightness(1);
  }
  50% { 
    transform: scale(1.1); 
    filter: brightness(1.2) drop-shadow(0 0 8px rgba(102, 126, 234, 0.5));
  }
}

.footer-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.copyright-text {
  font-size: 14px;
  color: var(--footer-text);
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  flex-wrap: wrap;
  animation: textGlow 4s ease-in-out infinite;
}

.university-text {
  font-size: 13px;
  color: var(--footer-text-muted);
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  opacity: 0.8;
}

.developer-name {
  font-weight: 600;
  color: var(--footer-accent);
  transition: var(--transition-smooth);
}

.developer-name:hover {
  color: #fff;
  text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
}

.student-id {
  font-family: 'Courier New', monospace;
  background: rgba(102, 126, 234, 0.2);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  border: 1px solid rgba(102, 126, 234, 0.3);
}

@keyframes textGlow {
  0%, 100% { filter: brightness(1); }
  50% { filter: brightness(1.1); }
}

/* Footer Decoration */
.footer-decoration {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.decoration-line {
  width: 60px;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--footer-accent), transparent);
  animation: lineGlow 3s ease-in-out infinite;
}

@keyframes lineGlow {
  0%, 100% { 
    opacity: 0.5; 
    transform: scaleX(1);
  }
  50% { 
    opacity: 1; 
    transform: scaleX(1.2);
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
  }
}

.decoration-dots {
  display: flex;
  gap: 8px;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--footer-accent);
  animation: dotPulse 2s ease-in-out infinite;
}

.dot:nth-child(2) {
  animation-delay: 0.3s;
}

.dot:nth-child(3) {
  animation-delay: 0.6s;
}

@keyframes dotPulse {
  0%, 100% { 
    opacity: 0.4; 
    transform: scale(1);
  }
  50% { 
    opacity: 1; 
    transform: scale(1.3);
    box-shadow: 0 0 8px rgba(102, 126, 234, 0.6);
  }
}

/* Floating Particles */
.footer-particles {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  overflow: hidden;
}

.particle {
  position: absolute;
  width: 4px;
  height: 4px;
  background: rgba(102, 126, 234, 0.3);
  border-radius: 50%;
  animation: particleFloat 8s linear infinite;
}

.particle:nth-child(1) {
  left: 10%;
  animation-delay: 0s;
  animation-duration: 8s;
}

.particle:nth-child(2) {
  left: 30%;
  animation-delay: 2s;
  animation-duration: 10s;
}

.particle:nth-child(3) {
  left: 50%;
  animation-delay: 4s;
  animation-duration: 12s;
}

.particle:nth-child(4) {
  left: 70%;
  animation-delay: 6s;
  animation-duration: 9s;
}

.particle:nth-child(5) {
  left: 90%;
  animation-delay: 1s;
  animation-duration: 11s;
}

@keyframes particleFloat {
  0% {
    bottom: -10px;
    opacity: 0;
    transform: translateX(0) rotate(0deg);
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% {
    bottom: 100%;
    opacity: 0;
    transform: translateX(20px) rotate(360deg);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .modern-footer {
    padding: 24px 0 20px;
  }
  
  .footer-logo {
    flex-direction: column;
    gap: 8px;
  }
  
  .footer-brand {
    font-size: 16px;
  }
  
  .copyright-text {
    font-size: 12px;
    flex-direction: column;
    gap: 4px;
  }
  
  .university-text {
    font-size: 11px;
  }
}

/* Hover Effects */
.modern-footer:hover .footer-icon {
  animation-duration: 1s;
}

.modern-footer:hover .decoration-line {
  animation-duration: 1.5s;
}

.modern-footer:hover .particle {
  animation-duration: 6s;
}
</style>
