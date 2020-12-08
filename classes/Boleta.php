<?php

class Boleta
{
    /** @var int $id */
    private $id;
    /** @var string $fecha */
    private $fecha;
    /** @var string $cliente */
    private $cliente;
    /** @var string $receptor */
    private $receptor;
    /** @var string $telefono */
    private $telefono;
    /** @var string $direccion */
    private $direccion;
    /** @var string $tipo */
    private $tipo;
    /** @var string $metodoPago */
    private $metodoPago;
    /** @var array $paquetes */
    private $paquetes;
    /** @var string $costoPaquetes */
    private $costoPaquetes;
    /** @var string $costoTotal*/
    private $costoTotal;
    /** @var string $comentario*/
    private $comentario;

    public function __construct(string $fecha, string $cliente, string $receptor, string $telefono,
                                string $direccion, string $tipo, string $metodoPago, array $paquetes,
                                string $costoPaquetes, string $costoTotal, string $comentario)
    {
        $this->fecha = $fecha;
        $this->cliente = $cliente;
        $this->receptor = $receptor;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->tipo = $tipo;
        $this->metodoPago = $metodoPago;
        $this->paquetes = $paquetes;
        $this->costoPaquetes = $costoPaquetes;
        $this->costoTotal = $costoTotal;
        $this->comentario = $comentario;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFecha(): string
    {
        return $this->fecha;
    }

    /**
     * @return string
     */
    public function getCliente(): string
    {
        return $this->cliente;
    }

    /**
     * @return string
     */
    public function getReceptor(): string
    {
        return $this->receptor;
    }

    /**
     * @return string
     */
    public function getTelefono(): string
    {
        return $this->telefono;
    }

    /**
     * @return string
     */
    public function getDireccion(): string
    {
        return $this->direccion;
    }

    /**
     * @return string
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

    /**
     * @return string
     */
    public function getMetodoPago(): string
    {
        return $this->metodoPago;
    }

    /**
     * @return array
     */
    public function getPaquetes(): array
    {
        return $this->paquetes;
    }

    /**
     * @return string
     */
    public function getCostoPaquetes(): string
    {
        return $this->costoPaquetes;
    }

    /**
     * @return string
     */
    public function getCostoTotal(): string
    {
        return $this->costoTotal;
    }

    /**
     * @return string
     */
    public function getComentario(): string
    {
        return $this->comentario;
    }
}