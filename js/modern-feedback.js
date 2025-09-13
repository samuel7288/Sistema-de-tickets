/* ======================================
   SISTEMA DE FEEDBACK VISUAL MODERNO
   ====================================== */

// Utilidades para feedback visual moderno
const ModernFeedback = {
    
    // Mostrar feedback en un input específico
    showInputFeedback: function(inputId, message, type = 'error') {
        const feedbackId = inputId + 'Feedback';
        const feedback = document.getElementById(feedbackId);
        
        if (feedback) {
            feedback.className = `input-feedback ${type}`;
            feedback.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i> ${message}`;
            feedback.style.display = 'block';
            
            // Añadir clase al input
            const input = document.getElementById(inputId);
            if (input) {
                input.classList.remove('input-success', 'input-error');
                input.classList.add(`input-${type}`);
            }
        }
    },
    
    // Limpiar feedback de un input
    clearInputFeedback: function(inputId) {
        const feedbackId = inputId + 'Feedback';
        const feedback = document.getElementById(feedbackId);
        
        if (feedback) {
            feedback.style.display = 'none';
            feedback.innerHTML = '';
        }
        
        // Limpiar clases del input
        const input = document.getElementById(inputId);
        if (input) {
            input.classList.remove('input-success', 'input-error');
        }
    },
    
    // Mostrar alerta de validación general
    showValidationAlert: function(containerId, message, type = 'error') {
        const container = document.getElementById(containerId);
        if (container) {
            const alertClass = type;
            const iconClass = type === 'error' ? 'exclamation-triangle' : 
                             type === 'success' ? 'check-circle' : 
                             type === 'warning' ? 'exclamation-circle' : 'info-circle';
            
            container.className = `validation-alert ${alertClass}`;
            container.innerHTML = `
                <div class="alert-content">
                    <i class="fas fa-${iconClass} alert-icon"></i>
                    <span class="alert-message">${message}</span>
                </div>
            `;
            container.style.display = 'block';
            
            // Scroll suave hacia la alerta
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },
    
    // Limpiar alerta de validación
    clearValidationAlert: function(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.style.display = 'none';
            container.innerHTML = '';
        }
    },
    
    // Estados de loading para botones
    setButtonLoading: function(buttonId, loading = true) {
        const button = document.getElementById(buttonId);
        if (button) {
            if (loading) {
                button.classList.add('loading');
                button.disabled = true;
            } else {
                button.classList.remove('loading');
                button.disabled = false;
            }
        }
    },
    
    // Mostrar overlay de loading global
    showGlobalLoading: function(message = 'Procesando...') {
        const overlay = document.createElement('div');
        overlay.id = 'globalLoadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div style="text-align: center; color: #1a237e;">
                <div class="loading-spinner"></div>
                <p style="margin-top: 15px; font-weight: 600;">${message}</p>
            </div>
        `;
        document.body.appendChild(overlay);
    },
    
    // Ocultar overlay de loading global
    hideGlobalLoading: function() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    },
    
    // Validación en tiempo real para inputs
    setupRealTimeValidation: function(inputId, validationFunction) {
        const input = document.getElementById(inputId);
        if (input) {
            // Validar mientras el usuario escribe (debounced)
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const result = validationFunction(this.value);
                    if (result.valid) {
                        ModernFeedback.clearInputFeedback(inputId);
                        if (result.message) {
                            ModernFeedback.showInputFeedback(inputId, result.message, 'success');
                        }
                    } else {
                        ModernFeedback.showInputFeedback(inputId, result.message, 'error');
                    }
                }, 300);
            });
            
            // Validar al perder el foco
            input.addEventListener('blur', function() {
                const result = validationFunction(this.value);
                if (result.valid) {
                    ModernFeedback.clearInputFeedback(inputId);
                    if (result.message) {
                        ModernFeedback.showInputFeedback(inputId, result.message, 'success');
                    }
                } else {
                    ModernFeedback.showInputFeedback(inputId, result.message, 'error');
                }
            });
        }
    }
};

// Validadores específicos para edades
const EdadValidators = {
    
    validateNombre: function(nombre) {
        if (!nombre || nombre.trim().length === 0) {
            return { valid: false, message: 'El nombre es obligatorio' };
        }
        if (nombre.trim().length < 2) {
            return { valid: false, message: 'El nombre debe tener al menos 2 caracteres' };
        }
        if (nombre.trim().length > 50) {
            return { valid: false, message: 'El nombre no puede exceder 50 caracteres' };
        }
        return { valid: true, message: 'Nombre válido ✓' };
    },
    
    validateEdadMin: function(edad) {
        const edadNum = parseInt(edad);
        if (isNaN(edadNum)) {
            return { valid: false, message: 'Debe ingresar un número válido' };
        }
        if (edadNum < 0) {
            return { valid: false, message: 'La edad mínima no puede ser negativa' };
        }
        if (edadNum > 150) {
            return { valid: false, message: 'La edad mínima no puede ser mayor a 150 años' };
        }
        return { valid: true, message: 'Edad mínima válida ✓' };
    },
    
    validateEdadMax: function(edad) {
        const edadNum = parseInt(edad);
        if (isNaN(edadNum)) {
            return { valid: false, message: 'Debe ingresar un número válido' };
        }
        if (edadNum < 0) {
            return { valid: false, message: 'La edad máxima no puede ser negativa' };
        }
        if (edadNum > 150) {
            return { valid: false, message: 'La edad máxima no puede ser mayor a 150 años' };
        }
        return { valid: true, message: 'Edad máxima válida ✓' };
    },
    
    validateRangeConsistency: function(edadMin, edadMax) {
        const minNum = parseInt(edadMin);
        const maxNum = parseInt(edadMax);
        
        if (isNaN(minNum) || isNaN(maxNum)) {
            return { valid: false, message: 'Ambas edades deben ser números válidos' };
        }
        
        if (minNum >= maxNum) {
            return { valid: false, message: 'La edad mínima debe ser menor que la edad máxima' };
        }
        
        return { valid: true, message: 'Rango de edades consistente ✓' };
    }
};

// Inicializar validaciones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Configurar validaciones en tiempo real si estamos en la página de edades
    if (document.getElementById('nombre')) {
        ModernFeedback.setupRealTimeValidation('nombre', EdadValidators.validateNombre);
        ModernFeedback.setupRealTimeValidation('edadMin', EdadValidators.validateEdadMin);
        ModernFeedback.setupRealTimeValidation('edadMax', EdadValidators.validateEdadMax);
        
        // Validación cruzada de rango de edades
        document.getElementById('edadMax').addEventListener('blur', function() {
            const edadMin = document.getElementById('edadMin').value;
            const edadMax = this.value;
            
            if (edadMin && edadMax) {
                const result = EdadValidators.validateRangeConsistency(edadMin, edadMax);
                if (!result.valid) {
                    ModernFeedback.showValidationAlert('validacionEdades', result.message, 'error');
                } else {
                    ModernFeedback.clearValidationAlert('validacionEdades');
                }
            }
        });
    }
    
    // Añadir animaciones de entrada
    const cards = document.querySelectorAll('.form-card, .table-card, .page-header');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });
});

// Notificaciones toast modernas
const ModernToast = {
    show: function(message, type = 'info', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `modern-toast toast-${type}`;
        
        const iconMap = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${iconMap[type]} toast-icon"></i>
                <span class="toast-message">${message}</span>
                <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Estilos inline para el toast
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            animation: slideInRight 0.4s ease-out;
            background: white;
            border-left: 5px solid ${type === 'success' ? '#4caf50' : 
                                      type === 'error' ? '#f44336' : 
                                      type === 'warning' ? '#ff9800' : '#2196f3'};
        `;
        
        document.body.appendChild(toast);
        
        // Auto-remove
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.4s ease-out';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 400);
        }, duration);
    }
};

// CSS para toasts (inyectado dinámicamente)
const toastStyles = `
<style>
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
}

.toast-icon {
    font-size: 1.25rem;
}

.toast-message {
    flex: 1;
    font-weight: 500;
    color: #2c3e50;
}

.toast-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #6c757d;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.toast-close:hover {
    background: #f8f9fa;
    color: #495057;
}
</style>
`;

// Inyecar estilos de toast
document.head.insertAdjacentHTML('beforeend', toastStyles);