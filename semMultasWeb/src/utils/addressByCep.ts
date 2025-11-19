export const fetchAddressByCep = async (cep: string) => {
    if (!cep) return;
    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();

        if (data.erro) {
            throw new Error('CEP inválido');
        }

        return data;
    } catch (error) {
        return null;
    }
};
