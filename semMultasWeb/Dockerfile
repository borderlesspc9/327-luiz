FROM node:20-alpine AS builder

WORKDIR /app

# Argumento para URL da API (deve ser passado no build)
ARG VITE_API_URL=http://localhost:8000/api

# Definir como variável de ambiente para o Vite
ENV VITE_API_URL=$VITE_API_URL

# Copiar arquivos de dependências primeiro (para cache)
COPY package*.json ./

# Instalar dependências
RUN npm ci --legacy-peer-deps || npm install

# Copiar código fonte
COPY . .

# Criar arquivo .env.production com a URL da API
RUN echo "VITE_API_URL=$VITE_API_URL" > .env.production

# Verificar se a variável está definida
RUN echo "Building with VITE_API_URL=$VITE_API_URL" && \
    cat .env.production

# Build da aplicação (usar vite build diretamente)
RUN npx vite build || npm run build-only || npm run build

# Stage de produção
FROM nginx:alpine

# Copiar arquivos buildados
COPY --from=builder /app/dist /usr/share/nginx/html

# Copiar configuração do nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
