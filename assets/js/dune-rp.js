/**
 * Dune RP Plugin - Main JavaScript File
 * Handles UI interactions and immersive effects
 */

class DuneRpManager {
    constructor() {
        this.spiceAnimations = [];
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeSpiceEffects();
            this.setupFormValidation();
            this.initializeEventListeners();
            this.startAmbientEffects();
        });
    }

    /**
     * Initialize spice-related visual effects
     */
    initializeSpiceEffects() {
        // Animate spice meters
        const spiceMeters = document.querySelectorAll('.spice-meter');
        spiceMeters.forEach(meter => {
            this.animateSpiceMeter(meter);
        });

        // Create floating spice particles
        this.createSpiceParticles();
    }

    animateSpiceMeter(meter) {
        const fill = meter.querySelector('.spice-fill');
        if (fill) {
            const targetWidth = fill.dataset.percentage || '0';
            let currentWidth = 0;
            
            const animate = () => {
                if (currentWidth < targetWidth) {
                    currentWidth += 1;
                    fill.style.width = currentWidth + '%';
                    requestAnimationFrame(animate);
                }
            };
            
            animate();
        }
    }

    createSpiceParticles() {
        const container = document.querySelector('.dune-container');
        if (!container) return;

        setInterval(() => {
            const particle = document.createElement('div');
            particle.className = 'spice-particle';
            particle.style.cssText = `
                position: fixed;
                width: 2px;
                height: 2px;
                background: #FFD700;
                pointer-events: none;
                border-radius: 50%;
                left: ${Math.random() * 100}vw;
                top: 100vh;
                z-index: 1;
                box-shadow: 0 0 6px #FFD700;
            `;
            
            container.appendChild(particle);
            
            // Animate particle upward
            particle.animate([
                { transform: 'translateY(0) scale(0)', opacity: 0 },
                { transform: 'translateY(-100vh) scale(1)', opacity: 1 },
                { transform: 'translateY(-200vh) scale(0)', opacity: 0 }
            ], {
                duration: 8000 + Math.random() * 4000,
                easing: 'linear'
            }).onfinish = () => {
                particle.remove();
            };
        }, 300);
    }

    /**
     * Setup form validation with thematic styling
     */
    setupFormValidation() {
        const forms = document.querySelectorAll('.dune-form');
        forms.forEach(form => {
            this.enhanceForm(form);
        });
    }

    enhanceForm(form) {
        const inputs = form.querySelectorAll('.dune-input, .dune-select, .dune-textarea');
        
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.boxShadow = '0 0 15px rgba(255, 215, 0, 0.5)';
                input.style.borderColor = '#FFD700';
            });
            
            input.addEventListener('blur', () => {
                input.style.boxShadow = '';
                input.style.borderColor = '#D6A65F';
            });
            
            // Real-time validation
            input.addEventListener('input', () => {
                this.validateField(input);
            });
        });
    }

    validateField(input) {
        const value = input.value.trim();
        const type = input.type || input.tagName.toLowerCase();
        let isValid = true;
        let message = '';

        switch (type) {
            case 'email':
                isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                message = isValid ? '' : 'Format email invalide';
                break;
            case 'text':
                if (input.hasAttribute('required') && value.length < 3) {
                    isValid = false;
                    message = 'Minimum 3 caractères requis';
                }
                break;
            case 'number':
                const num = parseFloat(value);
                const min = input.getAttribute('min');
                const max = input.getAttribute('max');
                if (min && num < parseFloat(min)) {
                    isValid = false;
                    message = `Valeur minimum: ${min}`;
                }
                if (max && num > parseFloat(max)) {
                    isValid = false;
                    message = `Valeur maximum: ${max}`;
                }
                break;
        }

        this.showFieldValidation(input, isValid, message);
    }

    showFieldValidation(input, isValid, message) {
        const existingError = input.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }

        if (!isValid && message) {
            const errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            errorElement.textContent = message;
            errorElement.style.cssText = `
                color: #f44336;
                font-size: 0.8em;
                margin-top: 5px;
                font-family: var(--font-dune);
            `;
            input.parentNode.appendChild(errorElement);
            input.style.borderColor = '#f44336';
        } else {
            input.style.borderColor = isValid ? '#4caf50' : '#D6A65F';
        }
    }

    /**
     * Initialize event listeners for interactive elements
     */
    initializeEventListeners() {
        // House card interactions
        const houseCards = document.querySelectorAll('.house-card');
        houseCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                this.playHoverSound();
                this.createHoverEffect(card);
            });
        });

        // Character avatars with blue eyes effect
        const avatars = document.querySelectorAll('.character-avatar');
        avatars.forEach(avatar => {
            avatar.addEventListener('mouseenter', () => {
                this.addBlueEyesEffect(avatar);
            });
            avatar.addEventListener('mouseleave', () => {
                this.removeBlueEyesEffect(avatar);
            });
        });

        // Spice transaction animations
        const spiceButtons = document.querySelectorAll('[data-spice-action]');
        spiceButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleSpiceAction(e);
            });
        });

        // Auto-refresh for real-time updates
        this.setupAutoRefresh();
    }

    createHoverEffect(element) {
        const effect = document.createElement('div');
        effect.className = 'hover-energy';
        effect.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,215,0,0.1), transparent);
            pointer-events: none;
            animation: energy-sweep 0.6s ease-out;
        `;
        
        element.style.position = 'relative';
        element.appendChild(effect);
        
        setTimeout(() => {
            effect.remove();
        }, 600);
    }

    addBlueEyesEffect(avatar) {
        if (avatar.querySelector('.blue-eyes-overlay')) return;
        
        const overlay = document.createElement('div');
        overlay.className = 'blue-eyes-overlay';
        overlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 35% 35%, rgba(30,74,140,0.8) 2px, transparent 3px),
                        radial-gradient(circle at 65% 35%, rgba(30,74,140,0.8) 2px, transparent 3px);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
        `;
        
        avatar.appendChild(overlay);
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
    }

    removeBlueEyesEffect(avatar) {
        const overlay = avatar.querySelector('.blue-eyes-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }

    handleSpiceAction(event) {
        const button = event.target;
        const action = button.dataset.spiceAction;
        const amount = button.dataset.spiceAmount || 0;
        
        // Visual feedback
        this.createSpiceTransferAnimation(button, amount);
        
        // Prevent double-clicks
        button.disabled = true;
        setTimeout(() => {
            button.disabled = false;
        }, 1000);
    }

    createSpiceTransferAnimation(element, amount) {
        const particles = Math.min(parseInt(amount) / 100, 20);
        const rect = element.getBoundingClientRect();
        
        for (let i = 0; i < particles; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: 4px;
                height: 4px;
                background: #FFD700;
                border-radius: 50%;
                left: ${rect.left + rect.width/2}px;
                top: ${rect.top + rect.height/2}px;
                pointer-events: none;
                z-index: 1000;
                box-shadow: 0 0 8px #FFD700;
            `;
            
            document.body.appendChild(particle);
            
            const angle = (i / particles) * Math.PI * 2;
            const distance = 100 + Math.random() * 100;
            const endX = Math.cos(angle) * distance;
            const endY = Math.sin(angle) * distance;
            
            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${endX}px, ${endY}px) scale(0)`, opacity: 0 }
            ], {
                duration: 1000 + Math.random() * 500,
                easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
            }).onfinish = () => {
                particle.remove();
            };
        }
    }

    playHoverSound() {
        // Simple audio feedback (optional)
        if ('AudioContext' in window) {
            const audioContext = new AudioContext();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.1;
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.1);
        }
    }

    setupAutoRefresh() {
        // Auto-refresh spice amounts every 30 seconds
        setInterval(() => {
            const spiceElements = document.querySelectorAll('[data-auto-refresh="spice"]');
            if (spiceElements.length > 0) {
                this.refreshSpiceData();
            }
        }, 30000);
    }

    async refreshSpiceData() {
        try {
            const response = await fetch('/dune-rp/api/spice/current');
            const data = await response.json();
            
            const spiceElements = document.querySelectorAll('[data-auto-refresh="spice"]');
            spiceElements.forEach(element => {
                if (data.character_spice) {
                    element.textContent = this.formatSpice(data.character_spice);
                    this.animateSpiceUpdate(element);
                }
            });
        } catch (error) {
            console.log('Spice data refresh failed:', error);
        }
    }

    formatSpice(amount) {
        if (amount >= 1000000) {
            return (amount / 1000000).toFixed(1) + 'M';
        } else if (amount >= 1000) {
            return (amount / 1000).toFixed(1) + 'K';
        }
        return amount.toString();
    }

    animateSpiceUpdate(element) {
        element.style.animation = 'none';
        element.offsetHeight; // Trigger reflow
        element.style.animation = 'spice-pulse 1s ease-in-out';
    }

    startAmbientEffects() {
        // Subtle background animations
        this.createStarField();
        this.animateDesertWinds();
    }

    createStarField() {
        const container = document.querySelector('.dune-container');
        if (!container) return;

        const starField = document.createElement('div');
        starField.className = 'star-field';
        starField.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        `;

        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.style.cssText = `
                position: absolute;
                width: 1px;
                height: 1px;
                background: white;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                opacity: ${Math.random() * 0.8 + 0.2};
                animation: twinkle ${2 + Math.random() * 3}s infinite;
            `;
            starField.appendChild(star);
        }

        container.appendChild(starField);
    }

    animateDesertWinds() {
        // Subtle sand particle movement
        setInterval(() => {
            const container = document.querySelector('.dune-container');
            if (!container) return;

            const sand = document.createElement('div');
            sand.style.cssText = `
                position: fixed;
                width: 1px;
                height: 1px;
                background: rgba(214, 166, 95, 0.3);
                left: -10px;
                top: ${Math.random() * 100}%;
                pointer-events: none;
                z-index: 1;
            `;

            container.appendChild(sand);

            sand.animate([
                { transform: 'translateX(0)', opacity: 0 },
                { transform: `translateX(${window.innerWidth + 20}px)`, opacity: 0.6 },
                { transform: `translateX(${window.innerWidth + 100}px)`, opacity: 0 }
            ], {
                duration: 15000 + Math.random() * 10000,
                easing: 'linear'
            }).onfinish = () => {
                sand.remove();
            };
        }, 200);
    }
}

// CSS Animations via JavaScript injection
const style = document.createElement('style');
style.textContent = `
    @keyframes energy-sweep {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    @keyframes twinkle {
        0%, 100% { opacity: 0.2; }
        50% { opacity: 1; }
    }
    
    .spice-particle {
        animation: float-up 6s linear forwards;
    }
    
    @keyframes float-up {
        0% { transform: translateY(0) rotateZ(0deg); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100vh) rotateZ(360deg); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize the Dune RP Manager
window.duneRP = new DuneRpManager();

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DuneRpManager;
}
